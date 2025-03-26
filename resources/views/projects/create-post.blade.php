@extends('layouts.app')

@section('title', 'Criar Novo Post')

@section('styles')
<style>
    .model-card {
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .model-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .model-card.selected {
        border-color: var(--primary-color);
        background-color: rgba(67, 72, 215, 0.05);
    }
    
    .model-icon {
        font-size: 2rem;
        margin-bottom: 10px;
        color: var(--primary-color);
    }
    
    /* Reduzir o tamanho dos cards de modelo */
    .model-card .card-body {
        padding: 1rem;
    }
    
    .model-card .card-title {
        margin-bottom: 0;
        font-size: 1rem;
    }
    
    .form-section {
        display: none;
    }
    
    .form-section.active {
        display: block;
    }
    
    .tab-content {
        padding: 20px 0;
    }
    
    .nav-tabs .nav-link.active {
        font-weight: bold;
        border-bottom: 3px solid var(--primary-color);
    }
    
    .context-checkbox {
        margin-bottom: 10px;
    }
    
    .form-section {
        margin-bottom: 30px;
    }
    
    .form-section-title {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
    }
    
    .template-card {
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }
    
    .template-card.selected {
        border-color: var(--primary-color);
        background-color: rgba(67, 72, 215, 0.05);
    }
    
    .length-option {
        text-align: center;
        padding: 15px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .length-option:hover {
        background-color: rgba(67, 72, 215, 0.05);
    }
    
    .length-option.selected {
        background-color: rgba(67, 72, 215, 0.1);
        border: 1px solid var(--primary-color);
    }
    
    .length-option i {
        font-size: 24px;
        margin-bottom: 10px;
        color: var(--primary-color);
    }
    
    .tone-option {
        padding: 10px 15px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-right: 10px;
        margin-bottom: 10px;
        display: inline-block;
    }
    
    .tone-option:hover {
        background-color: rgba(67, 72, 215, 0.05);
    }
    
    .tone-option.selected {
        background-color: rgba(67, 72, 215, 0.1);
        border: 1px solid var(--primary-color);
    }
    
    #generateBtn {
        min-width: 200px;
    }
    
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        color: white;
        display: none;
    }
    
    .loading-spinner {
        width: 80px;
        height: 80px;
        border: 8px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: var(--primary-color);
        animation: spin 1s ease-in-out infinite;
        margin-bottom: 20px;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .loading-text {
        font-size: 18px;
        margin-bottom: 10px;
    }
    
    .loading-subtext {
        font-size: 14px;
        opacity: 0.8;
    }
    
    .progress-container {
        width: 80%;
        max-width: 400px;
        margin-top: 20px;
    }
    
    .progress-bar {
        height: 6px;
        background-color: var(--primary-color);
        width: 0%;
        border-radius: 3px;
        transition: width 0.3s ease;
    }
</style>
@endsection

@section('content')
    <div class="content-header">
        <h1>Criar Novo Post para "{{ $project->title }}"</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Coluna da esquerda -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Escolha um modelo de post</h5>
                </div>
                <div class="card-body">
                    <div class="row row-cols-2 row-cols-md-5 g-2">
                        @foreach($postModels as $key => $model)
                        <div class="col">
                            <div class="card model-card {{ $postType == $key ? 'selected' : '' }}" data-model="{{ $key }}">
                                <div class="card-body text-center">
                                    <div class="model-icon">
                                        <i class="{{ $model['icon'] }}"></i>
                                    </div>
                                    <h5 class="card-title">{{ $model['name'] }}</h5>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Detalhes do Post</h5>
                </div>
                <div class="card-body">
                    <form id="postForm" action="{{ route('projects.generate-post', $project) }}" method="POST">
                        @csrf
                        <input type="hidden" name="post_type" id="post_type" value="{{ $postType }}">
                        <input type="hidden" name="length" id="post_length" value="1500">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Título do Post <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                id="title" name="title" value="{{ old('title') }}" required
                                placeholder="Escreva o título do seu artigo aqui">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="keywords" class="form-label">Palavras-chave <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('keywords') is-invalid @enderror" 
                                id="keywords" name="keywords" value="{{ old('keywords') }}" required
                                placeholder="Palavras-chave separadas por vírgula">
                            @error('keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Adicione palavras-chave relevantes para o seu post, separadas por vírgula.</div>
                        </div>
                        
                        <!-- Verifique se o campo 'topic' está presente no formulário -->
                        <div class="mb-3">
                            <label for="topic" class="form-label">Tópico do Post <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="topic" name="topic" required 
                                   placeholder="Ex: Benefícios da meditação para a saúde mental">
                            <div class="form-text">O assunto principal do seu post.</div>
                        </div>
                        
                        <!-- Abas para Contexto e Produtos -->
                        <ul class="nav nav-tabs" id="contextTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="context-tab" data-bs-toggle="tab" 
                                    data-bs-target="#context-content" type="button" role="tab" 
                                    aria-controls="context-content" aria-selected="true">
                                    Adicionar Contexto
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="products-tab" data-bs-toggle="tab" 
                                    data-bs-target="#products-content" type="button" role="tab" 
                                    aria-controls="products-content" aria-selected="false">
                                    Adicionar Produtos Amazon
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="contextTabsContent">
                            <!-- Aba de Contexto -->
                            <div class="tab-pane fade show active" id="context-content" role="tabpanel" aria-labelledby="context-tab">
                                <div class="mb-3">
                                    <p>Simplesmente copie e cole a URL do site aqui e clique em "BAIXAR DA URL"</p>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="context_url" name="context_url" 
                                            placeholder="Cole a URL do site aqui">
                                        <button class="btn btn-outline-primary" type="button" id="getContextButton">
                                            <i class="fas fa-download me-1"></i> BAIXAR DA URL
                                        </button>
                                    </div>
                                    <p class="text-muted small">Atenção! O conteúdo extrairá todo o texto, então se você não revisar e seu resultado tiver informações de concorrentes... a responsabilidade é sua, não da ferramenta.</p>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="context" class="form-label">
                                        <i class="fas fa-file-alt me-1"></i> Contexto Principal ou Dados para o Post:
                                    </label>
                                    <textarea class="form-control" id="context" name="context" rows="6" 
                                        placeholder="Dados e informações">{{ old('context') }}</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="context_outline" class="form-label">
                                        <i class="fas fa-list-ol me-1"></i> Contexto ou Estrutura para Tópicos (Esboços):
                                    </label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="1" id="add_outlines_context" name="add_outlines_context">
                                        <label class="form-check-label" for="add_outlines_context">
                                            Usar como Contexto para Esboços
                                        </label>
                                    </div>
                                    <textarea class="form-control" id="context_outline" name="context_outline" rows="4" 
                                        placeholder="Copie e cole aqui a lista do seu esboço&#10;título do esboço 1&#10;título do esboço 2&#10;título do esboço 3">{{ old('context_outline') }}</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="additional_prompt" class="form-label">Comandos Adicionais para IA:</label>
                                    <textarea class="form-control" id="additional_prompt" name="additional_prompt" rows="2" 
                                        placeholder="Escreva instruções curtas (como prompts para IA) para geração de texto">{{ old('additional_prompt') }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Aba de Produtos Amazon -->
                            <div class="tab-pane fade" id="products-content" role="tabpanel" aria-labelledby="products-tab">
                                <div class="mb-3 mt-3">
                                    <label for="search_amazon_kw" class="form-label">Buscar Produto na Amazon <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="search_amazon_kw" name="search_amazon_kw" 
                                            placeholder="Fones de ouvido sem fio">
                                        <select class="form-select" name="domain" style="max-width: 200px;">
                                            <option value="com.br" selected>Brasil (amazon.com.br)</option>
                                            <option value="com">Estados Unidos (amazon.com)</option>
                                            <option value="co.uk">Reino Unido (amazon.co.uk)</option>
                                            <option value="de">Alemanha (amazon.de)</option>
                                            <option value="es">Espanha (amazon.es)</option>
                                            <option value="fr">França (amazon.fr)</option>
                                        </select>
                                    </div>
                                    <div class="d-grid gap-2 d-md-flex">
                                        <button class="btn btn-outline-primary" type="button" id="searchAmazonButton">
                                            <i class="fas fa-search me-1"></i> BUSCAR PRODUTOS
                                        </button>
                                        <button class="btn btn-outline-primary" type="button" id="grabAmazonData">
                                            <i class="fas fa-download me-1"></i> LER DADOS DA AMAZON
                                        </button>
                                    </div>
                                </div>
                                
                                <div id="amazonProductsList" class="mt-3">
                                    <!-- Aqui serão exibidos os produtos da Amazon -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <!-- Coluna da direita -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Configurações de Geração</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3">
                            <i class="fas fa-cog me-1"></i> Escolha Como Gerar:
                        </h6>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="ai_model" class="form-label">Modelo de IA:</label>
                                <select class="form-select" id="ai_model" name="ai_model">
                                    <option value="gpt-4" selected>OpenAI GPT-4o (Recomendado)</option>
                                    <option value="llama-3">META-LLaMA-3.3 70B</option>
                                    <option value="deepseek">DeepSeek V3</option>
                                    <option value="claude">Claude Sonnet 3.7</option>
                                    <option value="qwen">Qwen2-VL (by Alibaba)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="output_language" class="form-label">Idioma de Saída:</label>
                                <select class="form-select" id="output_language" name="output_language">
                                    <option value="en">Inglês</option>
                                    <option value="pt-br" selected>Português (Brasil)</option>
                                    <option value="es">Espanhol</option>
                                    <option value="fr">Francês</option>
                                    <option value="de">Alemão</option>
                                    <option value="it">Italiano</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="pov" class="form-label">Ponto de Vista:</label>
                                <select class="form-select" id="pov" name="pov">
                                    <option value="first-person">Primeira pessoa</option>
                                    <option value="second-person" selected>Segunda pessoa</option>
                                    <option value="third-person">Terceira pessoa</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="tone" class="form-label">Tom:</label>
                                <select class="form-select" id="tone" name="tone">
                                    <option value="informative" selected>Informativo</option>
                                    <option value="educational">Educacional</option>
                                    <option value="friendly">Amigável</option>
                                    <option value="witty">Espirituoso</option>
                                    <option value="scientific">Científico</option>
                                    <option value="urban">Estilo Urbano</option>
                                    <option value="creative">Muito Criativo</option>
                                    <option value="poetry">Poético</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="add_bold" class="form-label">Adicionar <strong>Negrito</strong>/<i>Itálico</i>:</label>
                                <select class="form-select" id="add_bold" name="add_bold">
                                    <option value="0" selected>Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="faq" class="form-label">Adicionar FAQ:</label>
                                <select class="form-select" id="faq" name="faq">
                                    <option value="0" selected>Não</option>
                                    <option value="3">3 Respostas</option>
                                    <option value="5">5 Respostas</option>
                                    <option value="7">7 Respostas</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="keytakeaway" class="form-label">Adicionar Pontos-Chave:</label>
                                <select class="form-select" id="keytakeaway" name="keytakeaway">
                                    <option value="0" selected>Não</option>
                                    <option value="3">3 Itens</option>
                                    <option value="5">5 Itens</option>
                                    <option value="7">7 Itens</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="category_id" class="form-label">Categoria:</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Selecione uma categoria</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <h6 class="mb-3">
                            <i class="fas fa-text-height me-1"></i> Tamanho do Artigo
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="outlines_length" class="form-label">Tamanho dos Esboços:</label>
                                <select class="form-select" id="outlines_length" name="outlines_length">
                                    <option value="5">Curto (~500-1000 palavras)</option>
                                    <option value="1" selected>Médio (~1000-3000 palavras)</option>
                                    <option value="2">Longo (~3000-5000 palavras)</option>
                                    <option value="3">Extra Longo (~5000+ palavras)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="intro_length" class="form-label">Tamanho da Introdução:</label>
                                <select class="form-select" id="intro_length" name="intro_length">
                                    <option value="1" selected>Curta</option>
                                    <option value="2">Média</option>
                                    <option value="3">Longa</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="paragraphs_length" class="form-label">Tamanho dos Parágrafos:</label>
                                <select class="form-select" id="paragraphs_length" name="paragraphs_length">
                                    <option value="1" selected>Curto (~1 parágrafo)</option>
                                    <option value="2">Médio (~2-3 parágrafos)</option>
                                    <option value="3">Longo (~3 parágrafos)</option>
                                </select>
                            </div>
                        </div>
                        
                        <h6 class="mb-3">
                            <i class="fas fa-image me-1"></i> Gerar Imagens com IA
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="ai_thumbnail" class="form-label">Gerar Imagem Destacada:</label>
                                <select class="form-select" id="ai_thumbnail" name="ai_thumbnail">
                                    <option value="0" selected>Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="ai_image_location" class="form-label">Gerar Imagens no Artigo:</label>
                                <select class="form-select" id="ai_image_location" name="ai_image_location">
                                    <option value="0" selected>Não</option>
                                    <option value="1">1 Imagem no corpo do artigo</option>
                                    <option value="2">2 Imagens em parágrafos aleatórios</option>
                                    <option value="3">3 Imagens em parágrafos aleatórios</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="ai_image_style" class="form-label">Estilo da Foto:</label>
                                <select class="form-select" id="ai_image_style" name="ai_image_style">
                                    <option value="0" selected>Selecione um estilo</option>
                                    <option value="7">Foto Ultra-realista</option>
                                    <option value="2">Cena Cinematográfica</option>
                                    <option value="1">Foto Estilo iPhone</option>
                                    <option value="10">Foto de Produto</option>
                                    <option value="5">Arte Fantástica</option>
                                    <option value="6">Anime</option>
                                </select>
                            </div>
                        </div>
                        
                        <h6 class="mb-3">
                            <i class="fas fa-link me-1"></i> Adicionar Links Externos
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="external_links" class="form-label">Quantos Links:</label>
                                <select class="form-select" id="external_links" name="external_links">
                                    <option value="0" selected>Não</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="99">Aleatório</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="external_location" class="form-label">Localização dos Links:</label>
                                <select class="form-select" id="external_location" name="external_location">
                                    <option value="0" selected>Selecione</option>
                                    <option value="1">No Parágrafo de Introdução</option>
                                    <option value="2">No Meio</option>
                                    <option value="3">No Parágrafo de Conclusão</option>
                                    <option value="99">Aleatório</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="generatePostBtn">
                                <i class="fas fa-magic me-1"></i> GERAR POST
                            </button>
                            <p class="text-center text-muted small mt-2">Após pressionar "GERAR POST", o processo de geração do artigo começará automaticamente</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <div class="loading-text">Gerando seu conteúdo...</div>
        <div class="loading-subtext">Isso pode levar alguns minutos.</div>
        <div class="progress-container">
            <div class="progress-bar" id="progressBar"></div>
        </div>
    </div>

    <!-- Modal para exibir dados enviados para a IA (apenas para desenvolvimento) -->
    <div class="modal fade" id="aiDataModal" tabindex="-1" aria-labelledby="aiDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aiDataModalLabel">Dados enviados para a IA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre id="aiDataContent" style="max-height: 400px; overflow-y: auto;"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Adicione este botão temporário para teste -->
    <button type="button" class="btn btn-secondary" onclick="testForm()">
        Testar Formulário
    </button>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Selecionar modelo de post
        $('.model-card').on('click', function() {
            const modelType = $(this).data('model');
            
            // Atualizar seleção visual
            $('.model-card').removeClass('selected');
            $(this).addClass('selected');
            
            // Atualizar campo oculto
            $('#post_type').val(modelType);
            
            // Redirecionar para a mesma página com o tipo selecionado
            window.location.href = '{{ route("projects.create-post", $project) }}?type=' + modelType;
        });
        
        // Botão para obter contexto da URL
        $('#getContextButton').on('click', function() {
            const url = $('#context_url').val();
            if (url) {
                $(this).html('<i class="fas fa-spinner fa-spin me-1"></i> Carregando...');
                $(this).prop('disabled', true);
                
                // Fazer uma requisição AJAX para baixar o conteúdo da URL
                $.ajax({
                    url: '{{ route("download.url") }}',
                    type: 'POST',
                    data: {
                        url: url,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#context').val(response.content);
                        } else {
                            alert('Erro: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Erro ao processar a URL';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert(errorMessage);
                    },
                    complete: function() {
                        $('#getContextButton').html('<i class="fas fa-download me-1"></i> BAIXAR DA URL');
                        $('#getContextButton').prop('disabled', false);
                    }
                });
            } else {
                alert('Por favor, insira uma URL válida.');
            }
        });
        
        // Botões da Amazon (simulação)
        $('#searchAmazonButton, #grabAmazonData').on('click', function() {
            const keyword = $('#search_amazon_kw').val();
            if (keyword) {
                $(this).html('<i class="fas fa-spinner fa-spin me-1"></i> Carregando...');
                $(this).prop('disabled', true);
                
                // Simulação de busca na Amazon
                setTimeout(function() {
                    let html = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i> Produtos encontrados para "' + keyword + '"</div>';
                    html += '<div class="row">';
                    for (let i = 1; i <= 3; i++) {
                        html += `
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <img src="https://via.placeholder.com/150" class="card-img-top" alt="Produto ${i}">
                                    <div class="card-body">
                                        <h6 class="card-title">${keyword} - Modelo ${i}</h6>
                                        <p class="card-text">R$ ${(Math.random() * 500 + 100).toFixed(2)}</p>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="${i}" id="product${i}" name="amazon_products[]">
                                            <label class="form-check-label" for="product${i}">
                                                Incluir no post
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    html += '</div>';
                    
                    $('#amazonProductsList').html(html);
                    $('#searchAmazonButton').html('<i class="fas fa-search me-1"></i> BUSCAR PRODUTOS');
                    $('#searchAmazonButton').prop('disabled', false);
                    $('#grabAmazonData').html('<i class="fas fa-download me-1"></i> LER DADOS DA AMAZON');
                    $('#grabAmazonData').prop('disabled', false);
                }, 2000);
            } else {
                alert('Por favor, insira uma palavra-chave para buscar produtos.');
            }
        });

        // Atualizar o comprimento do post com base no tamanho selecionado
        $('select[name="outlines_length"]').on('change', function() {
            const value = $(this).val();
            let length = 1500; // Médio por padrão
            
            switch (value) {
                case '5': // Curto
                    length = 800;
                    break;
                case '1': // Médio
                    length = 1500;
                    break;
                case '2': // Longo
                    length = 3000;
                    break;
                case '3': // Extra longo
                    length = 5000;
                    break;
            }
            
            $('#post_length').val(length);
        });
        
        // Enviar formulário
        $('#postForm').on('submit', function(e) {
            // Remover o preventDefault para permitir o envio do formulário
            // e.preventDefault();
            
            // Mostrar overlay de carregamento
            $('#loadingOverlay').show();
            
            // Iniciar a barra de progresso
            const progressBar = document.getElementById('progressBar');
            progressBar.style.width = '10%';
            
            // Simular progresso
            let progress = 10;
            const interval = setInterval(function() {
                progress += 5;
                if (progress <= 90) {
                    progressBar.style.width = progress + '%';
                }
                if (progress >= 90) {
                    clearInterval(interval);
                }
            }, 1000);
            
            // O formulário será enviado normalmente
        });
    });

    function testForm() {
        // Exibir os dados do formulário no console
        const formData = new FormData(document.getElementById('postForm'));
        const formObject = {};
        formData.forEach((value, key) => { formObject[key] = value });
        console.log('Dados do formulário:', formObject);
        
        // Verificar a URL de destino
        console.log('URL de destino:', document.getElementById('postForm').action);
        
        // Verificar se o formulário é válido
        console.log('Formulário válido:', document.getElementById('postForm').checkValidity());
    }
</script>
@endsection 
@extends('layouts.app')

@section('title', 'Criar Novo Post')

@section('styles')
<style>
    .model-card {
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .model-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .model-card.selected {
        border-color: var(--primary-color);
        background-color: rgba(67, 72, 215, 0.05);
    }
    
    .model-icon {
        font-size: 2rem;
        margin-bottom: 10px;
        color: var(--primary-color);
    }
    
    /* Reduzir o tamanho dos cards de modelo */
    .model-card .card-body {
        padding: 1rem;
    }
    
    .model-card .card-title {
        margin-bottom: 0;
        font-size: 1rem;
    }
    
    .form-section {
        display: none;
    }
    
    .form-section.active {
        display: block;
    }
    
    .tab-content {
        padding: 20px 0;
    }
    
    .nav-tabs .nav-link.active {
        font-weight: bold;
        border-bottom: 3px solid var(--primary-color);
    }
    
    .context-checkbox {
        margin-bottom: 10px;
    }
    
    .form-section {
        margin-bottom: 30px;
    }
    
    .form-section-title {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
    }
    
    .template-card {
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }
    
    .template-card.selected {
        border-color: var(--primary-color);
        background-color: rgba(67, 72, 215, 0.05);
    }
    
    .length-option {
        text-align: center;
        padding: 15px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .length-option:hover {
        background-color: rgba(67, 72, 215, 0.05);
    }
    
    .length-option.selected {
        background-color: rgba(67, 72, 215, 0.1);
        border: 1px solid var(--primary-color);
    }
    
    .length-option i {
        font-size: 24px;
        margin-bottom: 10px;
        color: var(--primary-color);
    }
    
    .tone-option {
        padding: 10px 15px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-right: 10px;
        margin-bottom: 10px;
        display: inline-block;
    }
    
    .tone-option:hover {
        background-color: rgba(67, 72, 215, 0.05);
    }
    
    .tone-option.selected {
        background-color: rgba(67, 72, 215, 0.1);
        border: 1px solid var(--primary-color);
    }
    
    #generateBtn {
        min-width: 200px;
    }
    
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        color: white;
        display: none;
    }
    
    .loading-spinner {
        width: 80px;
        height: 80px;
        border: 8px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: var(--primary-color);
        animation: spin 1s ease-in-out infinite;
        margin-bottom: 20px;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .loading-text {
        font-size: 18px;
        margin-bottom: 10px;
    }
    
    .loading-subtext {
        font-size: 14px;
        opacity: 0.8;
    }
    
    .progress-container {
        width: 80%;
        max-width: 400px;
        margin-top: 20px;
    }
    
    .progress-bar {
        height: 6px;
        background-color: var(--primary-color);
        width: 0%;
        border-radius: 3px;
        transition: width 0.3s ease;
    }
</style>
@endsection

@section('content')
    <div class="content-header">
        <h1>Criar Novo Post para "{{ $project->title }}"</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Coluna da esquerda -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Escolha um modelo de post</h5>
                </div>
                <div class="card-body">
                    <div class="row row-cols-2 row-cols-md-5 g-2">
                        @foreach($postModels as $key => $model)
                        <div class="col">
                            <div class="card model-card {{ $postType == $key ? 'selected' : '' }}" data-model="{{ $key }}">
                                <div class="card-body text-center">
                                    <div class="model-icon">
                                        <i class="{{ $model['icon'] }}"></i>
                                    </div>
                                    <h5 class="card-title">{{ $model['name'] }}</h5>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Detalhes do Post</h5>
                </div>
                <div class="card-body">
                    <form id="postForm" action="{{ route('projects.generate-post', $project) }}" method="POST">
                        @csrf
                        <input type="hidden" name="post_type" id="post_type" value="{{ $postType }}">
                        <input type="hidden" name="length" id="post_length" value="1500">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Título do Post <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                id="title" name="title" value="{{ old('title') }}" required
                                placeholder="Escreva o título do seu artigo aqui">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="keywords" class="form-label">Palavras-chave <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('keywords') is-invalid @enderror" 
                                id="keywords" name="keywords" value="{{ old('keywords') }}" required
                                placeholder="Palavras-chave separadas por vírgula">
                            @error('keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Adicione palavras-chave relevantes para o seu post, separadas por vírgula.</div>
                        </div>
                        
                        <!-- Verifique se o campo 'topic' está presente no formulário -->
                        <div class="mb-3">
                            <label for="topic" class="form-label">Tópico do Post <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="topic" name="topic" required 
                                   placeholder="Ex: Benefícios da meditação para a saúde mental">
                            <div class="form-text">O assunto principal do seu post.</div>
                        </div>
                        
                        <!-- Abas para Contexto e Produtos -->
                        <ul class="nav nav-tabs" id="contextTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="context-tab" data-bs-toggle="tab" 
                                    data-bs-target="#context-content" type="button" role="tab" 
                                    aria-controls="context-content" aria-selected="true">
                                    Adicionar Contexto
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="products-tab" data-bs-toggle="tab" 
                                    data-bs-target="#products-content" type="button" role="tab" 
                                    aria-controls="products-content" aria-selected="false">
                                    Adicionar Produtos Amazon
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="contextTabsContent">
                            <!-- Aba de Contexto -->
                            <div class="tab-pane fade show active" id="context-content" role="tabpanel" aria-labelledby="context-tab">
                                <div class="mb-3">
                                    <p>Simplesmente copie e cole a URL do site aqui e clique em "BAIXAR DA URL"</p>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="context_url" name="context_url" 
                                            placeholder="Cole a URL do site aqui">
                                        <button class="btn btn-outline-primary" type="button" id="getContextButton">
                                            <i class="fas fa-download me-1"></i> BAIXAR DA URL
                                        </button>
                                    </div>
                                    <p class="text-muted small">Atenção! O conteúdo extrairá todo o texto, então se você não revisar e seu resultado tiver informações de concorrentes... a responsabilidade é sua, não da ferramenta.</p>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="context" class="form-label">
                                        <i class="fas fa-file-alt me-1"></i> Contexto Principal ou Dados para o Post:
                                    </label>
                                    <textarea class="form-control" id="context" name="context" rows="6" 
                                        placeholder="Dados e informações">{{ old('context') }}</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="context_outline" class="form-label">
                                        <i class="fas fa-list-ol me-1"></i> Contexto ou Estrutura para Tópicos (Esboços):
                                    </label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" value="1" id="add_outlines_context" name="add_outlines_context">
                                        <label class="form-check-label" for="add_outlines_context">
                                            Usar como Contexto para Esboços
                                        </label>
                                    </div>
                                    <textarea class="form-control" id="context_outline" name="context_outline" rows="4" 
                                        placeholder="Copie e cole aqui a lista do seu esboço&#10;título do esboço 1&#10;título do esboço 2&#10;título do esboço 3">{{ old('context_outline') }}</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="additional_prompt" class="form-label">Comandos Adicionais para IA:</label>
                                    <textarea class="form-control" id="additional_prompt" name="additional_prompt" rows="2" 
                                        placeholder="Escreva instruções curtas (como prompts para IA) para geração de texto">{{ old('additional_prompt') }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Aba de Produtos Amazon -->
                            <div class="tab-pane fade" id="products-content" role="tabpanel" aria-labelledby="products-tab">
                                <div class="mb-3 mt-3">
                                    <label for="search_amazon_kw" class="form-label">Buscar Produto na Amazon <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="search_amazon_kw" name="search_amazon_kw" 
                                            placeholder="Fones de ouvido sem fio">
                                        <select class="form-select" name="domain" style="max-width: 200px;">
                                            <option value="com.br" selected>Brasil (amazon.com.br)</option>
                                            <option value="com">Estados Unidos (amazon.com)</option>
                                            <option value="co.uk">Reino Unido (amazon.co.uk)</option>
                                            <option value="de">Alemanha (amazon.de)</option>
                                            <option value="es">Espanha (amazon.es)</option>
                                            <option value="fr">França (amazon.fr)</option>
                                        </select>
                                    </div>
                                    <div class="d-grid gap-2 d-md-flex">
                                        <button class="btn btn-outline-primary" type="button" id="searchAmazonButton">
                                            <i class="fas fa-search me-1"></i> BUSCAR PRODUTOS
                                        </button>
                                        <button class="btn btn-outline-primary" type="button" id="grabAmazonData">
                                            <i class="fas fa-download me-1"></i> LER DADOS DA AMAZON
                                        </button>
                                    </div>
                                </div>
                                
                                <div id="amazonProductsList" class="mt-3">
                                    <!-- Aqui serão exibidos os produtos da Amazon -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <!-- Coluna da direita -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Configurações de Geração</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3">
                            <i class="fas fa-cog me-1"></i> Escolha Como Gerar:
                        </h6>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="ai_model" class="form-label">Modelo de IA:</label>
                                <select class="form-select" id="ai_model" name="ai_model">
                                    <option value="gpt-4" selected>OpenAI GPT-4o</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="output_language" class="form-label">Idioma de Saída:</label>
                                <select class="form-select" id="output_language" name="output_language">
                                    <option value="en">Inglês</option>
                                    <option value="pt-br" selected>Português (Brasil)</option>
                                    <option value="es">Espanhol</option>
                                    <option value="fr">Francês</option>
                                    <option value="de">Alemão</option>
                                    <option value="it">Italiano</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="pov" class="form-label">Ponto de Vista:</label>
                                <select class="form-select" id="pov" name="pov">
                                    <option value="first-person">Primeira pessoa</option>
                                    <option value="second-person" selected>Segunda pessoa</option>
                                    <option value="third-person">Terceira pessoa</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="tone" class="form-label">Tom:</label>
                                <select class="form-select" id="tone" name="tone">
                                    <option value="informative" selected>Informativo</option>
                                    <option value="educational">Educacional</option>
                                    <option value="friendly">Amigável</option>
                                    <option value="witty">Espirituoso</option>
                                    <option value="scientific">Científico</option>
                                    <option value="urban">Estilo Urbano</option>
                                    <option value="creative">Muito Criativo</option>
                                    <option value="poetry">Poético</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="add_bold" class="form-label">Adicionar <strong>Negrito</strong>/<i>Itálico</i>:</label>
                                <select class="form-select" id="add_bold" name="add_bold">
                                    <option value="0" selected>Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="faq" class="form-label">Adicionar FAQ:</label>
                                <select class="form-select" id="faq" name="faq">
                                    <option value="0" selected>Não</option>
                                    <option value="3">3 Respostas</option>
                                    <option value="5">5 Respostas</option>
                                    <option value="7">7 Respostas</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="keytakeaway" class="form-label">Adicionar Pontos-Chave:</label>
                                <select class="form-select" id="keytakeaway" name="keytakeaway">
                                    <option value="0" selected>Não</option>
                                    <option value="3">3 Itens</option>
                                    <option value="5">5 Itens</option>
                                    <option value="7">7 Itens</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="category_id" class="form-label">Categoria:</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Selecione uma categoria</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <h6 class="mb-3">
                            <i class="fas fa-text-height me-1"></i> Tamanho do Artigo
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="outlines_length" class="form-label">Tamanho dos Esboços:</label>
                                <select class="form-select" id="outlines_length" name="outlines_length">
                                    <option value="5">Curto (~500-1000 palavras)</option>
                                    <option value="1" selected>Médio (~1000-3000 palavras)</option>
                                    <option value="2">Longo (~3000-5000 palavras)</option>
                                    <option value="3">Extra Longo (~5000+ palavras)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="intro_length" class="form-label">Tamanho da Introdução:</label>
                                <select class="form-select" id="intro_length" name="intro_length">
                                    <option value="1" selected>Curta</option>
                                    <option value="2">Média</option>
                                    <option value="3">Longa</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="paragraphs_length" class="form-label">Tamanho dos Parágrafos:</label>
                                <select class="form-select" id="paragraphs_length" name="paragraphs_length">
                                    <option value="1" selected>Curto (~1 parágrafo)</option>
                                    <option value="2">Médio (~2-3 parágrafos)</option>
                                    <option value="3">Longo (~3 parágrafos)</option>
                                </select>
                            </div>
                        </div>
                        
                        <h6 class="mb-3">
                            <i class="fas fa-image me-1"></i> Gerar Imagens com IA
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="ai_thumbnail" class="form-label">Gerar Imagem Destacada:</label>
                                <select class="form-select" id="ai_thumbnail" name="ai_thumbnail">
                                    <option value="0" selected>Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="ai_image_location" class="form-label">Gerar Imagens no Artigo:</label>
                                <select class="form-select" id="ai_image_location" name="ai_image_location">
                                    <option value="0" selected>Não</option>
                                    <option value="1">1 Imagem no corpo do artigo</option>
                                    <option value="2">2 Imagens em parágrafos aleatórios</option>
                                    <option value="3">3 Imagens em parágrafos aleatórios</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="ai_image_style" class="form-label">Estilo da Foto:</label>
                                <select class="form-select" id="ai_image_style" name="ai_image_style">
                                    <option value="0" selected>Selecione um estilo</option>
                                    <option value="7">Foto Ultra-realista</option>
                                    <option value="2">Cena Cinematográfica</option>
                                    <option value="1">Foto Estilo iPhone</option>
                                    <option value="10">Foto de Produto</option>
                                    <option value="5">Arte Fantástica</option>
                                    <option value="6">Anime</option>
                                </select>
                            </div>
                        </div>
                        
                        <h6 class="mb-3">
                            <i class="fas fa-link me-1"></i> Adicionar Links Externos
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="external_links" class="form-label">Quantos Links:</label>
                                <select class="form-select" id="external_links" name="external_links">
                                    <option value="0" selected>Não</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="99">Aleatório</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="external_location" class="form-label">Localização dos Links:</label>
                                <select class="form-select" id="external_location" name="external_location">
                                    <option value="0" selected>Selecione</option>
                                    <option value="1">No Parágrafo de Introdução</option>
                                    <option value="2">No Meio</option>
                                    <option value="3">No Parágrafo de Conclusão</option>
                                    <option value="99">Aleatório</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="generatePostBtn">
                                <i class="fas fa-magic me-1"></i> GERAR POST
                            </button>
                            <p class="text-center text-muted small mt-2">Após pressionar "GERAR POST", o processo de geração do artigo começará automaticamente</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <div class="loading-text">Gerando seu conteúdo...</div>
        <div class="loading-subtext">Isso pode levar alguns minutos.</div>
        <div class="progress-container">
            <div class="progress-bar" id="progressBar"></div>
        </div>
    </div>

    <!-- Modal para exibir dados enviados para a IA (apenas para desenvolvimento) -->
    <div class="modal fade" id="aiDataModal" tabindex="-1" aria-labelledby="aiDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aiDataModalLabel">Dados enviados para a IA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre id="aiDataContent" style="max-height: 400px; overflow-y: auto;"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Adicione este botão temporário para teste -->
    <button type="button" class="btn btn-secondary" onclick="testForm()">
        Testar Formulário
    </button>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Selecionar modelo de post
        $('.model-card').on('click', function() {
            const modelType = $(this).data('model');
            
            // Atualizar seleção visual
            $('.model-card').removeClass('selected');
            $(this).addClass('selected');
            
            // Atualizar campo oculto
            $('#post_type').val(modelType);
            
            // Redirecionar para a mesma página com o tipo selecionado
            window.location.href = '{{ route("projects.create-post", $project) }}?type=' + modelType;
        });
        
        // Botão para obter contexto da URL
        $('#getContextButton').on('click', function() {
            const url = $('#context_url').val();
            if (url) {
                $(this).html('<i class="fas fa-spinner fa-spin me-1"></i> Carregando...');
                $(this).prop('disabled', true);
                
                // Fazer uma requisição AJAX para baixar o conteúdo da URL
                $.ajax({
                    url: '{{ route("download.url") }}',
                    type: 'POST',
                    data: {
                        url: url,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#context').val(response.content);
                        } else {
                            alert('Erro: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Erro ao processar a URL';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert(errorMessage);
                    },
                    complete: function() {
                        $('#getContextButton').html('<i class="fas fa-download me-1"></i> BAIXAR DA URL');
                        $('#getContextButton').prop('disabled', false);
                    }
                });
            } else {
                alert('Por favor, insira uma URL válida.');
            }
        });
        
        // Botões da Amazon (simulação)
        $('#searchAmazonButton, #grabAmazonData').on('click', function() {
            const keyword = $('#search_amazon_kw').val();
            if (keyword) {
                $(this).html('<i class="fas fa-spinner fa-spin me-1"></i> Carregando...');
                $(this).prop('disabled', true);
                
                // Simulação de busca na Amazon
                setTimeout(function() {
                    let html = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i> Produtos encontrados para "' + keyword + '"</div>';
                    html += '<div class="row">';
                    for (let i = 1; i <= 3; i++) {
                        html += `
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <img src="https://via.placeholder.com/150" class="card-img-top" alt="Produto ${i}">
                                    <div class="card-body">
                                        <h6 class="card-title">${keyword} - Modelo ${i}</h6>
                                        <p class="card-text">R$ ${(Math.random() * 500 + 100).toFixed(2)}</p>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="${i}" id="product${i}" name="amazon_products[]">
                                            <label class="form-check-label" for="product${i}">
                                                Incluir no post
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    html += '</div>';
                    
                    $('#amazonProductsList').html(html);
                    $('#searchAmazonButton').html('<i class="fas fa-search me-1"></i> BUSCAR PRODUTOS');
                    $('#searchAmazonButton').prop('disabled', false);
                    $('#grabAmazonData').html('<i class="fas fa-download me-1"></i> LER DADOS DA AMAZON');
                    $('#grabAmazonData').prop('disabled', false);
                }, 2000);
            } else {
                alert('Por favor, insira uma palavra-chave para buscar produtos.');
            }
        });

        // Atualizar o comprimento do post com base no tamanho selecionado
        $('select[name="outlines_length"]').on('change', function() {
            const value = $(this).val();
            let length = 1500; // Médio por padrão
            
            switch (value) {
                case '5': // Curto
                    length = 800;
                    break;
                case '1': // Médio
                    length = 1500;
                    break;
                case '2': // Longo
                    length = 3000;
                    break;
                case '3': // Extra longo
                    length = 5000;
                    break;
            }
            
            $('#post_length').val(length);
        });
        
        // Enviar formulário
        $('#postForm').on('submit', function(e) {
            // Remover o preventDefault para permitir o envio do formulário
            // e.preventDefault();
            
            // Mostrar overlay de carregamento
            $('#loadingOverlay').show();
            
            // Iniciar a barra de progresso
            const progressBar = document.getElementById('progressBar');
            progressBar.style.width = '10%';
            
            // Simular progresso
            let progress = 10;
            const interval = setInterval(function() {
                progress += 5;
                if (progress <= 90) {
                    progressBar.style.width = progress + '%';
                }
                if (progress >= 90) {
                    clearInterval(interval);
                }
            }, 1000);
            
            // O formulário será enviado normalmente
        });
    });

    function testForm() {
        // Exibir os dados do formulário no console
        const formData = new FormData(document.getElementById('postForm'));
        const formObject = {};
        formData.forEach((value, key) => { formObject[key] = value });
        console.log('Dados do formulário:', formObject);
        
        // Verificar a URL de destino
        console.log('URL de destino:', document.getElementById('postForm').action);
        
        // Verificar se o formulário é válido
        console.log('Formulário válido:', document.getElementById('postForm').checkValidity());
    }
</script>
@endsection 