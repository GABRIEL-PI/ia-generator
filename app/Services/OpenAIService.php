<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $apiKey;
    protected $model;
    protected $maxTokens;
    protected $temperature;
    
    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        if (empty($this->apiKey)) {
            Log::error('API Key da OpenAI não configurada');
            throw new \Exception('API Key da OpenAI não configurada');
        }
        
        $this->model = config('services.openai.model', 'gpt-4');
        $this->maxTokens = config('services.openai.max_tokens', 4000);
        $this->temperature = config('services.openai.temperature', 0.7);
        
        Log::info('OpenAIService inicializado', [
            'model' => $this->model,
            'maxTokens' => $this->maxTokens
        ]);
    }
    
    /**
     * Gera um post usando a API da OpenAI
     */
    public function generatePost($prompt, $options = [])
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $options['model'] ?? $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Você é um escritor profissional especializado em criar conteúdo para blogs. Seu trabalho é criar conteúdo detalhado, informativo e envolvente que seja otimizado para SEO.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => $options['max_tokens'] ?? $this->maxTokens,
                'temperature' => $options['temperature'] ?? $this->temperature,
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                return $result['choices'][0]['message']['content'];
            } else {
                Log::error('Erro na API da OpenAI: ' . $response->body());
                throw new \Exception('Erro ao gerar conteúdo: ' . $response->json()['error']['message'] ?? 'Erro desconhecido');
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao chamar a API da OpenAI: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Gera um título usando a API da OpenAI
     */
    public function generateTitle($topic, $options = [])
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $options['model'] ?? $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Você é um especialista em criar títulos atraentes e otimizados para SEO.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Crie um título atraente e otimizado para SEO para um artigo sobre o seguinte tópico: {$topic}. Retorne apenas o título, sem aspas ou formatação adicional."
                    ]
                ],
                'max_tokens' => 50,
                'temperature' => 0.8,
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                $title = $result['choices'][0]['message']['content'];
                // Remover aspas se existirem
                return trim(str_replace('"', '', $title));
            } else {
                Log::error('Erro na API da OpenAI: ' . $response->body());
                throw new \Exception('Erro ao gerar título: ' . $response->json()['error']['message'] ?? 'Erro desconhecido');
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao chamar a API da OpenAI: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Gera uma imagem usando a API DALL-E da OpenAI
     */
    public function generateImage($prompt, $options = [])
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/images/generations', [
                'prompt' => $prompt,
                'n' => $options['n'] ?? 1,
                'size' => $options['size'] ?? '1024x1024',
                'response_format' => 'url',
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                return $result['data'][0]['url'];
            } else {
                Log::error('Erro na API DALL-E: ' . $response->body());
                throw new \Exception('Erro ao gerar imagem: ' . $response->json()['error']['message'] ?? 'Erro desconhecido');
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao chamar a API DALL-E: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Gera conteúdo usando a API da OpenAI
     *
     * @param string $prompt O prompt para gerar o conteúdo
     * @param string $model O modelo a ser usado (opcional)
     * @return string O conteúdo gerado
     */
    public function generateContent($prompt, $model = null)
    {
        $model = $model ?? config('services.openai.model', 'gpt-4-turbo');
        $apiKey = config('services.openai.api_key');
        
        if (empty($apiKey)) {
            throw new \Exception('Chave de API da OpenAI não configurada.');
        }
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->withOptions([
                'verify' => !app()->environment('local') // Desativa verificação SSL em ambiente local
            ])
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Você é um assistente especializado em criar conteúdo para blogs em português brasileiro.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                return $result['choices'][0]['message']['content'];
            } else {
                Log::error('Erro na API da OpenAI: ' . $response->body());
                throw new \Exception('Erro ao gerar conteúdo: ' . $response->json()['error']['message'] ?? 'Erro desconhecido');
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao chamar a API da OpenAI: ' . $e->getMessage());
            throw $e;
        }
    }
} 