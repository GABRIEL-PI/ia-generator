<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\WordPressSite;
use App\Models\Post;
use App\Models\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\OpenAIService;

class ProjectController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    /**
     * Exibe a lista de projetos do usuário
     */
    public function index()
    {
        $projects = Project::where('user_id', Auth::id())->get();
        return view('projects.index', compact('projects'));
    }
    
    /**
     * Exibe o formulário para criar um novo projeto
     */
    public function create()
    {
        $wordPressSites = WordPressSite::where('user_id', Auth::id())->get();
        
        if ($wordPressSites->isEmpty()) {
            return redirect()->route('wordpress.create')
                ->with('warning', 'Você precisa conectar um site WordPress antes de criar um projeto.');
        }
        
        return view('projects.create', compact('wordPressSites'));
    }
    
    /**
     * Armazena um novo projeto
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'wordpress_site_id' => 'required|exists:wordpress_sites,id',
            'settings' => 'nullable|array'
        ]);
        
        // Verificar se o site pertence ao usuário
        $site = WordPressSite::findOrFail($validated['wordpress_site_id']);
        if ($site->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para usar este site WordPress.');
        }
        
        $project = Project::create([
            'title' => $validated['title'],
            'wordpress_site_id' => $validated['wordpress_site_id'],
            'user_id' => Auth::id(),
            'settings' => $validated['settings'] ?? []
        ]);
        
        return redirect()->route('projects.show', $project)
            ->with('success', 'Projeto criado com sucesso!');
    }
    
    /**
     * Exibe um projeto específico
     */
    public function show(Project $project)
    {
        // Verificar se o usuário tem permissão para ver este projeto
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para acessar este projeto.');
        }
        
        // Carregar o relacionamento com o site WordPress
        $project->load('wordPressSite');
        
        $posts = $project->posts()->orderBy('created_at', 'desc')->get();
        return view('projects.show', compact('project', 'posts'));
    }
    
    /**
     * Exibe o formulário para criar um novo post
     */
    public function createPost(Project $project, Request $request)
    {
        // Verificar se o usuário tem permissão para ver este projeto
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para acessar este projeto.');
        }
        
        // Obter o tipo de post da query string ou definir um padrão
        $postType = $request->query('type', 'informative');
        
        // Definir os modelos de posts disponíveis
        $postModels = [
            'informative' => [
                'name' => 'Informativo',
                'icon' => 'fas fa-info-circle',
                'description' => 'Artigo informativo com fatos e dados sobre o tema'
            ],
            'tutorial' => [
                'name' => 'Tutorial',
                'icon' => 'fas fa-book',
                'description' => 'Guia passo a passo para ensinar algo'
            ],
            'review' => [
                'name' => 'Análise',
                'icon' => 'fas fa-star',
                'description' => 'Avaliação detalhada de um produto ou serviço'
            ],
            'listicle' => [
                'name' => 'Lista',
                'icon' => 'fas fa-list',
                'description' => 'Artigo em formato de lista com tópicos numerados'
            ],
            'opinion' => [
                'name' => 'Opinião',
                'icon' => 'fas fa-comment',
                'description' => 'Artigo com ponto de vista pessoal sobre um tema'
            ]
        ];
        
        // Obter categorias do WordPress
        try {
            $site = $project->wordPressSite;
            $response = Http::withBasicAuth(
                $site->username, 
                $site->api_token
            )->get($site->url . '/wp-json/api-connector/v1/categories');
            
            if ($response->successful()) {
                $categories = $response->json();
            } else {
                $categories = [];
            }
        } catch (\Exception $e) {
            $categories = [];
        }
        
        return view('projects.create-post', compact('project', 'categories', 'postType', 'postModels'));
    }
    
    /**
     * Gera um novo post usando a API da OpenAI
     */
    public function generatePost(Request $request, Project $project)
    {
        // Verificar se o usuário tem permissão para este projeto
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para acessar este projeto.');
        }

        \Log::info('Dados recebidos para geração de post:', $request->all());

        try {
            // Coleta dos dados do formulário
            $title             = $request->input('title');
            $topic             = $request->input('topic') ?? $title ?? 'Novo Post';
            $keywords          = $request->input('keywords');
            $context           = $request->input('context');         // Contexto Principal ou Dados para o Post
            $contextOutline    = $request->input('context_outline');   // Esboço ou estrutura para tópicos
            $additionalPrompt  = $request->input('additional_prompt'); // Comandos Adicionais para IA

            // Configurações de Geração
            $aiModel           = $request->input('ai_model');
            $outputLanguage    = $request->input('output_language');
            $pov               = $request->input('pov');
            $tone              = $request->input('tone');
            $addBold           = $request->input('add_bold');
            $faq               = $request->input('faq');
            $keyTakeaway       = $request->input('keytakeaway');
            $categoryId        = $request->input('category_id');

            // Configurações de Tamanho
            $outlinesLength    = $request->input('outlines_length');
            $introLength       = $request->input('intro_length');
            $paragraphsLength  = $request->input('paragraphs_length');
            // É possível mapear os valores para descrições se necessário

            // Opções de Imagens
            $aiThumbnail       = $request->input('ai_thumbnail');
            $aiImageLocation   = $request->input('ai_image_location');
            $aiImageStyle      = $request->input('ai_image_style');

            // Opções de Links Externos
            $externalLinks     = $request->input('external_links');
            $externalLocation  = $request->input('external_location');

            // Construir o prompt base utilizando os dados coletados
            $prompt = "Crie um artigo completo sobre o seguinte tópico: \"{$topic}\".\n\n";

            if (!empty($keywords)) {
                $prompt .= "Palavras-chave a serem incluídas: {$keywords}.\n";
            }

            if (!empty($tone)) {
                $prompt .= "O tom do conteúdo deve ser: {$tone}.\n";
            }

            if (!empty($pov)) {
                $prompt .= "Utilize o ponto de vista: {$pov}.\n";
            }

            // Incluir o contexto principal, se houver
            if (!empty($context)) {
                $prompt .= "\nContexto principal ou dados para o post: {$context}\n";
            }

            // Incluir a estrutura do esboço, se fornecida
            if (!empty($contextOutline)) {
                $prompt .= "\nEstrutura para tópicos (esboços): {$contextOutline}\n";
            }

            if (!empty($additionalPrompt)) {
                $prompt .= "\nComandos adicionais para a IA: {$additionalPrompt}\n";
            }

            // Instruções gerais para o artigo
            $prompt .= "\nInstruções gerais:\n";
            $prompt .= "- O artigo deve ser 100% único, otimizado para SEO e conter aproximadamente {$outlinesLength} palavras (tamanho dos esboços pode orientar o tamanho do conteúdo).\n";
            $prompt .= "- Utilize subtítulos (H2, H3) para organizar o conteúdo e destaque os títulos com negrito.\n";
            //$prompt .= "- Crie duas tabelas em Markdown: a primeira com o esboço do artigo e a segunda (com título em negrito) com o artigo completo.\n";
            $prompt .= "- Crie uma introdução com três parágrafos: os dois primeiros com até 100 palavras cada e o terceiro com um CTA persuasivo (sem promessas exageradas).\n";
            $prompt .= "- Termine com uma conclusão e 5 perguntas frequentes (FAQ) únicas.\n";

            // Incluir as configurações de tamanho, se desejado
            $prompt .= "\nConfigurações adicionais:\n";
            $prompt .= "- Tamanho dos esboços: {$outlinesLength}.\n";
            $prompt .= "- Tamanho da introdução: {$introLength}.\n";
            $prompt .= "- Tamanho dos parágrafos: {$paragraphsLength}.\n";

            // Incluir as configurações de imagens, se configuradas
            $prompt .= "\nOpções de Imagens:\n";
            $prompt .= "- Gerar imagem destacada: " . ($aiThumbnail == 1 ? 'Sim' : 'Não') . ".\n";
            $prompt .= "- Gerar imagens no artigo: {$aiImageLocation}.\n";
            $prompt .= "- Estilo da foto: {$aiImageStyle}.\n";

            // Incluir as configurações de links externos, se aplicável
            $prompt .= "\nLinks externos:\n";
            $prompt .= "- Quantidade de links: {$externalLinks}.\n";
            $prompt .= "- Localização dos links: {$externalLocation}.\n";

            // Incluir outras configurações, se houver
            $prompt .= "\nOutras configurações:\n";
            $prompt .= "- Modelo de IA: {$aiModel}.\n";
            $prompt .= "- Idioma de Saída: {$outputLanguage}.\n";
            $prompt .= "- FAQ a ser adicionado: {$faq} respostas.\n";
            $prompt .= "- Adicionar pontos-chave: " . ($keyTakeaway != 0 ? $keyTakeaway : 'Não') . ".\n";
            $prompt .= "- Categoria: " . (!empty($categoryId) ? $categoryId : 'Não definida') . ".\n";

            \Log::info('Prompt gerado:', ['prompt' => $prompt]);

            // Criação do post no banco de dados com os dados mínimos e configurações
            $post = Post::create([
                'title'      => $title,
                'content'    => 'Conteúdo em geração...',
                'project_id' => $project->id,
                'user_id'    => Auth::id(),
                'status'     => 'draft',
                'settings'   => [
                    'topic'             => $topic,
                    'keywords'          => $keywords,
                    'tone'              => $tone,
                    'pov'               => $pov,
                    'context'           => $context,
                    'context_outline'   => $contextOutline,
                    'additional_prompt' => $additionalPrompt,
                    'ai_model'          => $aiModel,
                    'output_language'   => $outputLanguage,
                    'faq'               => $faq,
                    'keytakeaway'       => $keyTakeaway,
                    'category_id'       => $categoryId,
                    'outlines_length'   => $outlinesLength,
                    'intro_length'      => $introLength,
                    'paragraphs_length' => $paragraphsLength,
                    'ai_thumbnail'      => $aiThumbnail,
                    'ai_image_location' => $aiImageLocation,
                    'ai_image_style'    => $aiImageStyle,
                    'external_links'    => $externalLinks,
                    'external_location' => $externalLocation,
                ]
            ]);

            // Gerar o conteúdo com a OpenAI
            try {
                $content = $this->generateContentWithoutSSLVerification($prompt);

                $post->update([
                    'content' => $content
                ]);

                \Log::info('Conteúdo gerado com sucesso para o post ID: ' . $post->id);
            } catch (\Exception $e) {
                \Log::error('Erro ao gerar conteúdo com a OpenAI: ' . $e->getMessage());
            }

            return redirect()->route('posts.preview', $post->id)
                ->with('success', 'Post criado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar post: ' . $e->getMessage(), [
                'exception'    => $e,
                'trace'        => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao gerar post: ' . $e->getMessage())
                ->withInput();
        }
    }




    /**
     * Gera conteúdo usando a API da OpenAI sem verificação SSL
     */
    private function generateContentWithoutSSLVerification($prompt)
    {
        $apiKey = env('OPENAI_API_KEY');
        $model = env('OPENAI_MODEL', 'gpt-4-turbo');

        $client = new \GuzzleHttp\Client([
            'verify' => false, // Desabilitar verificação SSL
        ]);

        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'model' => $model,
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => 'Você é um escritor profissional especializado em criar conteúdo para blogs. Seu trabalho é criar conteúdo detalhado, informativo e envolvente que seja otimizado para SEO.'
                    ],
                    [
                        'role'    => 'user',
                        'content' => $prompt, // Aqui você passa o prompt gerado
                    ]
                ],
                'max_tokens'  => 4000,
                'temperature' => 0.7,
            ],
        ]);

        $result = json_decode($response->getBody(), true);
        return $result['choices'][0]['message']['content'];
    }


    /**
     * Exibe a visualização de um post
     */
    public function previewPost($postId)
    {
        $post = Post::findOrFail($postId);
        
        // Verificar se o usuário tem permissão para ver este post
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para acessar este post.');
        }
        
        // Carregar o projeto, seus posts e o site WordPress
        $post->load(['project.posts', 'project.wordPressSite']);
        
        // Obter categorias do WordPress
        try {
            $site = $post->project->wordPressSite;
            $response = Http::withBasicAuth(
                $site->username, 
                $site->api_token
            )->withOptions([
                'verify' => false
            ])->get($site->url . '/wp-json/api-connector/v1/categories');
            
            if ($response->successful()) {
                $categories = $response->json();
            } else {
                $categories = [];
            }
        } catch (\Exception $e) {
            $categories = [];
        }
        
        // Contar palavras no conteúdo
        $wordCount = str_word_count(strip_tags($post->content));
        
        // Carregar todos os sites WordPress do usuário
        $wordPressSites = WordPressSite::where('user_id', Auth::id())->get();
        
        // Carregar todas as conexões do usuário
        $connections = Connection::where('user_id', Auth::id())->get();
        
        return view('posts.preview', compact('post', 'categories', 'wordCount', 'wordPressSites', 'connections'));
    }
    
    /**
     * Publica um post no WordPress
     */
    public function publishPost(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        
        // Verificar se o usuário tem permissão para publicar este post
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para publicar este post.');
        }
        
        $validated = $request->validate([
            'wordpress_site_id' => 'nullable|exists:wordpress_sites,id',
            'category_id' => 'nullable|string',
            'status' => 'required|in:publish,draft,pending',
            'featured_image' => 'nullable|string',
        ]);
        
        try {
            // Se não foi especificado um site WordPress, usar o do projeto
            if (empty($validated['wordpress_site_id'])) {
                $site = $post->project->wordPressSite;
            } else {
                $site = WordPressSite::findOrFail($validated['wordpress_site_id']);
                
                // Verificar se o usuário tem permissão para usar este site
                if ($site->user_id !== Auth::id()) {
                    abort(403, 'Você não tem permissão para usar este site WordPress.');
                }
            }
            
            // Preparar os dados para envio
            $postData = [
                'title' => $post->title,
                'content' => $post->content,
                'status' => $validated['status'],
            ];
            
            // Adicionar categoria apenas se for fornecida e não estiver vazia
            if (!empty($validated['category_id'])) {
                $postData['categories'] = [(int)$validated['category_id']];
            }
            
            // Adicionar imagem destacada apenas se for fornecida
            if (!empty($validated['featured_image'])) {
                $postData['featured_media'] = (int)$validated['featured_image'];
            }
            
            // Log dos dados que serão enviados
            \Log::info('Dados a serem enviados para o WordPress:', $postData);
            
            // Enviar para o WordPress
            $response = Http::withBasicAuth(
                $site->username, 
                $site->api_token
            )->withOptions([
                'verify' => false  // Desabilitar verificação SSL
            ])->post($site->url . '/wp-json/wp/v2/posts', $postData);
            
            // Log da resposta completa para depuração
            \Log::info('Resposta do WordPress:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            if ($response->successful()) {
                $wpPost = $response->json();
                
                // Atualizar o post com a URL do WordPress
                $post->update([
                    'wordpress_id' => $wpPost['id'] ?? null,
                    'wordpress_url' => $wpPost['link'] ?? null,
                    'status' => 'published'
                ]);
                
                return redirect()->back()->with('success', 'Post publicado com sucesso no WordPress!');
            } else {
                // Log do erro
                \Log::error('Erro ao publicar no WordPress: ' . $response->body());
                
                // Tentar extrair uma mensagem de erro mais amigável
                $errorMessage = 'Erro desconhecido';
                $responseData = $response->json();
                
                if (isset($responseData['message'])) {
                    $errorMessage = $responseData['message'];
                } elseif (isset($responseData['error'])) {
                    $errorMessage = $responseData['error'];
                }
                
                return redirect()->back()->with('error', 'Erro ao publicar no WordPress: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            // Log do erro
            \Log::error('Erro ao publicar post: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()->with('error', 'Erro ao publicar post: ' . $e->getMessage());
        }
    }
    
    /**
     * Baixa o conteúdo de uma URL
     */
    public function downloadUrl(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url'
        ]);
        
        try {
            // Usar uma biblioteca como HTML2Text para extrair o conteúdo
            $response = Http::withOptions([
                'verify' => false
            ])->get($validated['url']);
            
            if ($response->successful()) {
                $html = $response->body();
                
                // Extrair o conteúdo principal (isso é uma simplificação)
                // Em uma implementação real, você usaria uma biblioteca como Readability ou HTML2Text
                $dom = new \DOMDocument();
                @$dom->loadHTML($html);
                $xpath = new \DOMXPath($dom);
                
                // Tentar encontrar o conteúdo principal
                $content = '';
                $contentNodes = $xpath->query('//article|//main|//div[@class="content"]|//div[@id="content"]');
                
                if ($contentNodes->length > 0) {
                    $content = $dom->saveHTML($contentNodes->item(0));
                } else {
                    // Fallback: pegar o body inteiro
                    $bodyNodes = $xpath->query('//body');
                    if ($bodyNodes->length > 0) {
                        $content = $dom->saveHTML($bodyNodes->item(0));
                    }
                }
                
                // Limpar o HTML para obter apenas o texto
                $content = strip_tags($content, '<p><h1><h2><h3><h4><h5><h6><ul><ol><li>');
                
                return response()->json([
                    'success' => true,
                    'content' => $content
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível acessar a URL'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a URL: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Calcula o número de créditos usados com base nas opções selecionadas
     */
    private function calculateCreditsUsed($data)
    {
        $baseCredits = 500; // Créditos base para gerar um post
        $totalCredits = $baseCredits;
        
        // Adicionar créditos para imagens geradas por IA
        if (!empty($data['generate_images']['thumbnail'])) {
            $totalCredits += 250; // +250 para imagem de destaque
        }
        
        if (!empty($data['generate_images']['in_article'])) {
            $totalCredits += 250 * intval($data['generate_images']['in_article']); // +250 por imagem no artigo
        }
        
        // Adicionar créditos para links externos
        if (!empty($data['external_links']['count']) && $data['external_links']['count'] > 0) {
            $totalCredits += 250; // +250 para links externos
        }
        
        // Adicionar créditos para tamanho do post
        if ($data['outlines_length'] == 2) { // Longo
            $totalCredits += 250;
        } elseif ($data['outlines_length'] == 3) { // Extra longo
            $totalCredits += 500;
        }
        
        // Adicionar créditos para FAQ
        if (!empty($data['faq']) && $data['faq'] > 0) {
            $totalCredits += 100;
        }
        
        // Adicionar créditos para Key Takeaways
        if (!empty($data['keytakeaway']) && $data['keytakeaway'] > 0) {
            $totalCredits += 100;
        }
        
        return $totalCredits;
    }
    
    /**
     * Simula a geração de conteúdo com IA (para desenvolvimento)
     */
    private function simulateAIContent($data)
    {
        // Definir valores padrão para evitar erros de chaves indefinidas
        $data['add_bold'] = $data['settings']['add_bold'] ?? false;
        $data['faq'] = $data['settings']['faq'] ?? 0;
        $data['keytakeaway'] = $data['settings']['keytakeaway'] ?? 0;
        
        $postType = $data['post_type'] ?? 'informative';
        $paragraphs = 5; // Número padrão de parágrafos
        
        // Ajustar o número de parágrafos com base no comprimento
        if (isset($data['length'])) {
            if ($data['length'] <= 800) {
                $paragraphs = 3;
            } elseif ($data['length'] <= 1500) {
                $paragraphs = 5;
            } elseif ($data['length'] <= 3000) {
                $paragraphs = 8;
            } else {
                $paragraphs = 12;
            }
        }
        
        // Gerar o conteúdo
        // $content = "<h2>Introdução</h2>\n";
        // $content .= "<p>Este é um artigo sobre {$data['title']}. ";
        // $content .= "Este conteúdo foi gerado como um exemplo para demonstração. ";
        // $content .= "Em uma implementação real, este texto seria gerado por uma API de IA como GPT-4.</p>\n\n";
        
        // // Gerar conteúdo específico para cada tipo de post
        // for ($i = 1; $i <= $paragraphs; $i++) {
        //     switch ($postType) {
        //         case 'informative':
        //             $content .= "<h3>Tópico " . $i . "</h3>\n";
        //             $content .= "<p>Este é um parágrafo informativo sobre {$data['title']}. ";
        //             $content .= "Este conteúdo foi gerado como um exemplo no tom {$data['tone']}. ";
        //             if ($data['add_bold']) {
        //                 $content .= "Aqui temos um <strong>texto em negrito</strong> para destacar informações importantes. ";
        //             }
        //             $content .= "Em uma implementação real, este texto seria gerado por uma API de IA como GPT-4.</p>\n\n";
        //             break;
                    
        //         case 'tutorial':
        //             $content .= "<h3>Passo {$i}</h3>\n";
        //             $content .= "<p>Nesta etapa do tutorial sobre {$data['title']}, você precisa seguir estas instruções. ";
        //             $content .= "Este conteúdo foi gerado como um exemplo no tom {$data['tone']}. ";
        //             if ($data['add_bold']) {
        //                 $content .= "Lembre-se de <strong>verificar cada etapa</strong> antes de prosseguir. ";
        //             }
        //             $content .= "Em uma implementação real, este texto seria gerado por uma API de IA como GPT-4.</p>\n\n";
        //             break;
                    
        //         case 'review':
        //             if ($i == 1) {
        //                 $content .= "<h3>Visão Geral</h3>\n";
        //             } elseif ($i == 2) {
        //                 $content .= "<h3>Prós e Contras</h3>\n";
        //                 $content .= "<h4>Prós:</h4>\n<ul>\n<li>Vantagem 1</li>\n<li>Vantagem 2</li>\n<li>Vantagem 3</li>\n</ul>\n";
        //                 $content .= "<h4>Contras:</h4>\n<ul>\n<li>Desvantagem 1</li>\n<li>Desvantagem 2</li>\n</ul>\n";
        //             } elseif ($i == $paragraphs) {
        //                 $content .= "<h3>Conclusão</h3>\n";
        //             } else {
        //                 $content .= "<h3>Análise Detalhada - Parte " . ($i-2) . "</h3>\n";
        //             }
        //             $content .= "<p>Este é um parágrafo de análise sobre {$data['title']}. ";
        //             $content .= "Este conteúdo foi gerado como um exemplo no tom {$data['tone']}. ";
        //             if ($data['add_bold']) {
        //                 $content .= "A <strong>qualidade do produto</strong> é um fator determinante na avaliação. ";
        //             }
        //             $content .= "Em uma implementação real, este texto seria gerado por uma API de IA como GPT-4.</p>\n\n";
        //             break;
                    
        //         case 'listicle':
        //             $content .= "<h3>" . $i . ". Item da Lista</h3>\n";
        //             $content .= "<p>Este é um item da lista sobre {$data['title']}. ";
        //             $content .= "Este conteúdo foi gerado como um exemplo no tom {$data['tone']}. ";
        //             if ($data['add_bold']) {
        //                 $content .= "Este item <strong>se destaca</strong> pelos seguintes motivos. ";
        //             }
        //             $content .= "Em uma implementação real, este texto seria gerado por uma API de IA como GPT-4.</p>\n\n";
        //             break;
                    
        //         case 'opinion':
        //             if ($i == 1) {
        //                 $content .= "<h3>Minha Perspectiva</h3>\n";
        //             } elseif ($i == $paragraphs) {
        //                 $content .= "<h3>Considerações Finais</h3>\n";
        //             } else {
        //                 $content .= "<h3>Ponto " . $i . "</h3>\n";
        //             }
        //             $content .= "<p>Este é um parágrafo de opinião sobre {$data['title']}. ";
        //             $content .= "Este conteúdo foi gerado como um exemplo no tom {$data['tone']}. ";
        //             if ($data['add_bold']) {
        //                 $content .= "Eu <strong>acredito firmemente</strong> que este aspecto merece atenção. ";
        //             }
        //             $content .= "Em uma implementação real, este texto seria gerado por uma API de IA como GPT-4.</p>\n\n";
        //             break;
        //     }
        // }
        
        // Adicionar FAQ se solicitado
        if (!empty($data['faq']) && $data['faq'] > 0) {
            $content .= "<h3>Perguntas Frequentes</h3>\n";
            $content .= "<div class='faq-section'>\n";
            
            for ($i = 1; $i <= $data['faq']; $i++) {
                $content .= "<div class='faq-item'>\n";
                $content .= "<h4>Pergunta " . $i . " sobre {$data['title']}?</h4>\n";
                $content .= "<p>Esta é uma resposta detalhada para a pergunta " . $i . ". Em uma implementação real, tanto a pergunta quanto a resposta seriam geradas por uma API de IA como GPT-4, com base no contexto fornecido.</p>\n";
                $content .= "</div>\n";
            }
            
            $content .= "</div>\n\n";
        }
        
        // Adicionar Key Takeaways se solicitado
        if (!empty($data['keytakeaway']) && $data['keytakeaway'] > 0) {
            $content .= "<h3>Principais Conclusões</h3>\n";
            $content .= "<div class='key-takeaways'>\n<ul>\n";
            
            for ($i = 1; $i <= $data['keytakeaway']; $i++) {
                $content .= "<li>Conclusão importante " . $i . " sobre {$data['title']}. Esta é uma informação que você deve lembrar.</li>\n";
            }
            
            $content .= "</ul>\n</div>\n\n";
        }
        
        return $content;
    }
    
    /**
     * Registra o uso de créditos pelo usuário
     */
    private function logCreditUsage($userId, $action, $amount)
    {
        // Atualizar o saldo de créditos do usuário
        $user = \App\Models\User::find($userId);
        $user->credits_balance -= $amount;
        $user->credits_used += $amount;
        $user->save();
        
        // Registrar o uso de créditos
        \App\Models\CreditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'amount' => $amount,
            'description' => 'Geração de post',
            'created_at' => now()
        ]);
    }

    /**
     * Atualiza o conteúdo de um post
     */
    public function updatePost(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        
        // Verificar se o usuário tem permissão para editar este post
        if ($post->project->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para editar este post.'
            ], 403);
        }
        
        $validated = $request->validate([
            'content' => 'required|string'
        ]);
        
        try {
            $post->update([
                'content' => $validated['content']
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Post atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar o post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Agenda a publicação automática de posts
     */
    public function schedulePost(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'wordpress_site_id' => 'required|exists:wordpress_sites,id',
            'daily_articles' => 'required|integer|min:0|max:7'
        ]);
        
        $project = Project::findOrFail($validated['project_id']);
        
        // Verificar se o usuário tem permissão para este projeto
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para agendar posts para este projeto.');
        }
        
        // Verificar se o site WordPress pertence ao usuário
        $site = WordPressSite::findOrFail($validated['wordpress_site_id']);
        if ($site->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para usar este site WordPress.');
        }
        
        // Aqui você implementaria a lógica de agendamento
        // Por enquanto, apenas salvamos as configurações no projeto
        
        $project->update([
            'settings' => array_merge($project->settings ?? [], [
                'schedule' => [
                    'wordpress_site_id' => $validated['wordpress_site_id'],
                    'daily_articles' => $validated['daily_articles'],
                    'enabled' => true,
                    'created_at' => now()->toDateTimeString()
                ]
            ])
        ]);
        
        return redirect()->back()->with('success', 'Agendamento configurado com sucesso!');
    }

    /**
     * Reseta o agendamento de posts
     */
    public function resetSchedule(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id'
        ]);
        
        $project = Project::findOrFail($validated['project_id']);
        
        // Verificar se o usuário tem permissão para este projeto
        if ($project->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para resetar o agendamento deste projeto.'
            ], 403);
        }
        
        // Remover as configurações de agendamento
        $settings = $project->settings ?? [];
        if (isset($settings['schedule'])) {
            unset($settings['schedule']);
            $project->update(['settings' => $settings]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Agendamento resetado com sucesso!'
        ]);
    }

    /**
     * Constrói o prompt para a OpenAI com base nos parâmetros do formulário
     */
    private function buildPrompt($data)
    {
        $prompt = "Crie um artigo completo sobre o seguinte tópico: {$data['topic']}.\n\n";
        
        // Adicionar tipo de conteúdo
        if (!empty($data['type'])) {
            $typeMap = [
                'informative' => 'informativo',
                'tutorial' => 'tutorial passo a passo',
                'review' => 'análise/review',
                'listicle' => 'lista de itens (listicle)',
                'opinion' => 'opinião/editorial'
            ];
            
            $prompt .= "Tipo de conteúdo: " . ($typeMap[$data['type']] ?? 'informativo') . ".\n";
        }
        
        // Adicionar palavras-chave
        if (!empty($data['keywords'])) {
            $prompt .= "Palavras-chave a serem incluídas: {$data['keywords']}.\n";
        }
        
        // Adicionar tom
        if (!empty($data['tone'])) {
            $toneMap = [
                'professional' => 'profissional e formal',
                'casual' => 'casual e descontraído',
                'friendly' => 'amigável e acessível',
                'authoritative' => 'autoritativo e confiante',
                'humorous' => 'humorístico e leve'
            ];
            
            $prompt .= "Tom do conteúdo: " . ($toneMap[$data['tone']] ?? 'profissional') . ".\n";
        }
        
        // Adicionar comprimento
        if (!empty($data['length'])) {
            $lengthMap = [
                1 => 'curto (aproximadamente 500 palavras)',
                2 => 'médio (aproximadamente 800 palavras)',
                3 => 'padrão (aproximadamente 1000 palavras)',
                4 => 'longo (aproximadamente 1500 palavras)',
                5 => 'muito longo (aproximadamente 2000+ palavras)'
            ];
            
            $prompt .= "Comprimento: " . ($lengthMap[$data['length']] ?? 'padrão (aproximadamente 1000 palavras)') . ".\n";
        }
        
        // Adicionar número de seções
        if (!empty($data['sections'])) {
            $prompt .= "Divida o conteúdo em {$data['sections']} seções principais.\n";
        }
        
        // Adicionar pontos-chave
        if (!empty($data['keytakeaway']) && $data['keytakeaway'] > 0) {
            $prompt .= "Inclua {$data['keytakeaway']} pontos-chave (key takeaways) no final do artigo.\n";
        }
        
        // Adicionar FAQ
        if (!empty($data['include_faq'])) {
            $prompt .= "Inclua uma seção de Perguntas Frequentes (FAQ) com pelo menos 3 perguntas e respostas relevantes.\n";
        }
        
        // Adicionar índice
        if (!empty($data['include_toc'])) {
            $prompt .= "Inclua um índice (Table of Contents) no início do artigo.\n";
        }
        
        // Instruções adicionais para SEO e formatação
        $prompt .= "\nInstruções adicionais:
- O conteúdo deve ser otimizado para SEO.
- Use subtítulos (H2, H3) para organizar o conteúdo.
- Inclua uma introdução envolvente e uma conclusão.
- Formate o conteúdo em HTML, usando tags como <h2>, <h3>, <p>, <ul>, <ol>, <li>, <strong>, <em>, etc.
- Evite jargões excessivos e mantenha o texto acessível.
- Inclua exemplos práticos quando relevante.";
        
        return $prompt;
    }

    /**
     * Calcula os créditos usados com base nos parâmetros do post
     */
    private function calculateCredits($data)
    {
        // Base de créditos pelo comprimento
        $baseCredits = [
            1 => 1,  // curto
            2 => 2,  // médio
            3 => 3,  // padrão
            4 => 4,  // longo
            5 => 5   // muito longo
        ];
        
        $credits = $baseCredits[$data['length']];
        
        // Adicionar créditos extras para recursos adicionais
        if (!empty($data['include_faq']) && $data['include_faq']) {
            $credits += 1;
        }
        
        if (!empty($data['keytakeaway']) && $data['keytakeaway'] > 0) {
            $credits += 0.5;
        }
        
        return $credits;
    }

    /**
     * Gera imagens para um post usando a API DALL-E
     */
    public function generateImages(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        
        // Verificar se o usuário tem permissão para este post
        if ($post->project->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para gerar imagens para este post.'
            ], 403);
        }
        
        $validated = $request->validate([
            'prompt' => 'required|string',
            'count' => 'required|integer|min:1|max:10'
        ]);
        
        try {
            $images = [];
            
            // Gerar o número solicitado de imagens
            for ($i = 0; $i < $validated['count']; $i++) {
                $imageUrl = $this->openAIService->generateImage($validated['prompt'], [
                    'size' => '1024x1024'
                ]);
                
                $images[] = $imageUrl;
                
                // Adicionar um pequeno atraso para evitar limitações de taxa da API
                if ($i < $validated['count'] - 1) {
                    usleep(500000); // 0.5 segundos
                }
            }
            
            // Registrar o uso de créditos para imagens
            $this->logCreditUsage(Auth::id(), 'generate_images', $validated['count']);
            
            return response()->json([
                'success' => true,
                'images' => $images
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar imagens: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar imagens: ' . $e->getMessage()
            ], 500);
        }
    }
} 