@extends('layouts.app')

@section('title', 'Geração em Massa de Artigos')

@section('styles')
<style>
    .title-item {
        padding: 10px;
        border: 1px solid var(--bs-border-color);
        border-radius: 5px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .title-item .title-text {
        flex-grow: 1;
        margin-right: 10px;
    }

    .title-item .remove-title {
        cursor: pointer;
    }

    .titles-list {
        max-height: 300px;
        overflow-y: auto;
        margin-bottom: 20px;
    }

    .tab-content {
        padding-top: 20px;
    }

    .idea-radio-item {
        display: inline-block;
        margin-right: 10px;
        margin-bottom: 10px;
        cursor: pointer;
        padding: 8px 15px;
        border-radius: 20px;
        border: 1px solid var(--bs-border-color);
        transition: all 0.2s ease;
    }

    .idea-radio-item:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }

    .idea-radio-item.selected {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        border-color: var(--bs-primary);
    }

    .idea-radio-item input {
        margin-right: 5px;
    }

    .article-settings-section {
        background-color: var(--bs-body-bg);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .article-settings-section h4 {
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .article-settings-section h4 i {
        margin-right: 10px;
    }

    .titles-counter {
        font-size: 14px;
        color: var(--bs-secondary);
        margin-left: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="content-header d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-magic me-2"></i>Geração em Massa de Artigos</h1>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Seção de Geração de Títulos -->
            <div class="card mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="titleGenerationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="keywords-tab" data-bs-toggle="tab" data-bs-target="#keywords-content" type="button" role="tab" aria-controls="keywords-content" aria-selected="true">
                                <i class="fas fa-key me-1"></i> Palavras-chave
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="titles-tab" data-bs-toggle="tab" data-bs-target="#titles-content" type="button" role="tab" aria-controls="titles-content" aria-selected="false">
                                <i class="fas fa-heading me-1"></i> Títulos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="chatgpt-tab" data-bs-toggle="tab" data-bs-target="#chatgpt-content" type="button" role="tab" aria-controls="chatgpt-content" aria-selected="false">
                                <i class="fas fa-robot me-1"></i> ChatGPT
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual-content" type="button" role="tab" aria-controls="manual-content" aria-selected="false">
                                <i class="fas fa-edit me-1"></i> Manual
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="titleGenerationTabsContent">
                        <!-- Tab de Palavras-chave -->
                        <div class="tab-pane fade show active" id="keywords-content" role="tabpanel" aria-labelledby="keywords-tab">
                            <form id="generateForm">
                                <div class="mb-3">
                                    <label for="keywords" class="form-label">Palavras-chave Principais</label>
                                    <input type="text" class="form-control" id="keywords" required>
                                    <div class="form-text">Separe as palavras-chave por vírgulas</div>
                                </div>
                                <div class="mb-3">
                                    <label for="supportKeywords" class="form-label">Palavras-chave de Suporte (opcional)</label>
                                    <input type="text" class="form-control" id="supportKeywords">
                                    <div class="form-text">Palavras-chave secundárias para enriquecer o conteúdo</div>
                                </div>
                                <div class="mb-3">
                                    <label for="titleStyle" class="form-label">Estilo do Título</label>
                                    <select class="form-select" id="titleStyle" required>
                                        <option value="normal">Normal</option>
                                        <option value="question">Pergunta</option>
                                        <option value="guide">Guia/Tutorial</option>
                                        <option value="listicle">Lista Numerada</option>
                                        <option value="evergreen">Conteúdo Atemporal</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-magic me-1"></i> Gerar Títulos
                                </button>
                            </form>
                        </div>

                        <!-- Tab de Títulos -->
                        <div class="tab-pane fade" id="titles-content" role="tabpanel" aria-labelledby="titles-tab">
                            <form id="generateFromTitleForm">
                                <div class="mb-3">
                                    <label for="title-input" class="form-label">Título do Artigo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title-input" placeholder="Ex: Como Escrever Descrições de Produtos que Aumentam suas Margens de Lucro">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label d-block">Estilo de Título</label>
                                    <div class="idea-radio-item selected" onclick="selectRadioItem(this)">
                                        <input type="radio" name="titleStyle2" value="question" checked> Pergunta
                                    </div>
                                    <div class="idea-radio-item" onclick="selectRadioItem(this)">
                                        <input type="radio" name="titleStyle2" value="guide"> Guia
                                    </div>
                                    <div class="idea-radio-item" onclick="selectRadioItem(this)">
                                        <input type="radio" name="titleStyle2" value="evergreen"> Evergreen
                                    </div>
                                </div>
                                <button type="button" id="generateFromTitleBtn" class="btn btn-primary">
                                    <i class="fas fa-lightbulb me-1"></i> Gerar Ideias
                                </button>
                            </form>
                        </div>

                        <!-- Tab de ChatGPT -->
                        <div class="tab-pane fade" id="chatgpt-content" role="tabpanel" aria-labelledby="chatgpt-tab">
                            <form id="chatgptForm">
                                <div class="mb-3">
                                    <label for="chatgpt-prompt" class="form-label">Prompt para ChatGPT <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="chatgpt-prompt" rows="4" placeholder="Ex: Escreva uma lista de 10 ideias de tópicos curtos para blog sobre Labrador e Shampoo para Cães. Escreva os tópicos como perguntas.">Escreva uma lista de 10 ideias de tópicos curtos para blog sobre Labrador e Shampoo para Cães. Escreva os tópicos como perguntas.</textarea>
                                </div>
                                <button type="button" id="generateChatGPTBtn" class="btn btn-primary">
                                    <i class="fas fa-robot me-1"></i> Gerar com ChatGPT
                                </button>
                            </form>
                        </div>

                        <!-- Tab de Adição Manual -->
                        <div class="tab-pane fade" id="manual-content" role="tabpanel" aria-labelledby="manual-tab">
                            <form id="addTitlesForm">
                                <div class="mb-3">
                                    <label for="manualTitles" class="form-label">Adicionar Títulos Manualmente <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="manualTitles" rows="6" placeholder="Cole aqui sua lista de títulos, um por linha:&#10;Título 1&#10;Título 2&#10;Título 3"></textarea>
                                    <div class="form-text">O título deve ter mais de 4 palavras. Títulos mais curtos não serão aceitos.</div>
                                </div>
                                <button type="button" id="addManualTitlesBtn" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Adicionar Títulos
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Títulos -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i> Fila de Artigos para Geração
                        <span class="titles-counter" id="titlesCounter">0 títulos</span>
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="clearTitlesBtn">
                        <i class="fas fa-trash me-1"></i> Limpar
                    </button>
                </div>
                <div class="card-body">
                    <div class="titles-list" id="titlesList">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>Adicione títulos à lista de geração usando as opções acima.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Formulário de Configurações -->
            <form id="bulkGenerationForm" action="{{ route('projects.bulk-generate.store') }}" method="POST">
                @csrf
                <input type="hidden" name="titles" id="titlesInput" value="[]">

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i> Configurações de Geração</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="project_id" class="form-label">Projeto <span class="text-danger">*</span></label>
                            <select class="form-select" id="project_id" name="project_id" required>
                                <option value="">Selecione um projeto</option>
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Estilo do Artigo -->
                        <div class="article-settings-section">
                            <h4><i class="fas fa-pen-fancy"></i> Estilo do Artigo</h4>
                            <div class="mb-3">
                                <label class="form-label d-block">Escolha como Gerar:</label>
                                <div class="idea-radio-item selected" onclick="selectRadioItem(this)">
                                    <input type="radio" name="article_style" value="detect" checked> Detectar!
                                </div>
                                <div class="idea-radio-item" onclick="selectRadioItem(this)">
                                    <input type="radio" name="article_style" value="informative"> Informativo
                                </div>
                                <div class="idea-radio-item" onclick="selectRadioItem(this)">
                                    <input type="radio" name="article_style" value="guide"> Guia
                                </div>
                                <div class="idea-radio-item" onclick="selectRadioItem(this)">
                                    <input type="radio" name="article_style" value="howto"> How-to
                                </div>
                                <div class="idea-radio-item" onclick="selectRadioItem(this)">
                                    <input type="radio" name="article_style" value="tutorial"> Tutorial
                                </div>
                                <div class="idea-radio-item" onclick="selectRadioItem(this)">
                                    <input type="radio" name="article_style" value="listicle"> TOP 10 Listicle
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="language" class="form-label">Idioma de Saída:</label>
                                    <select class="form-select" id="language" name="language">
                                        <option value="en">Inglês</option>
                                        <option value="pt-br" selected>Português (Brasil)</option>
                                        <option value="es">Espanhol</option>
                                        <option value="fr">Francês</option>
                                        <option value="de">Alemão</option>
                                        <option value="it">Italiano</option>
                                        <option value="ja">Japonês</option>
                                        <option value="zh">Chinês (Simplificado)</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="pov" class="form-label">Ponto de Vista:</label>
                                    <select class="form-select" id="pov" name="pov">
                                        <option value="first">Primeira Pessoa</option>
                                        <option value="second" selected>Segunda Pessoa</option>
                                        <option value="third">Terceira Pessoa</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
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
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="words" class="form-label">Número de Palavras:</label>
                                    <select class="form-select" id="words" name="words">
                                        <option value="500">500 palavras</option>
                                        <option value="800">800 palavras</option>
                                        <option value="1000" selected>1000 palavras</option>
                                        <option value="1500">1500 palavras</option>
                                        <option value="2000">2000 palavras</option>
                                        <option value="3000">3000 palavras</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="faq" class="form-label">Adicionar FAQ:</label>
                                    <select class="form-select" id="faq" name="faq">
                                        <option value="0" selected>Não</option>
                                        <option value="3">3 Perguntas</option>
                                        <option value="5">5 Perguntas</option>
                                        <option value="7">7 Perguntas</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="key_takeaways" class="form-label">Pontos-chave:</label>
                                    <select class="form-select" id="key_takeaways" name="key_takeaways">
                                        <option value="0" selected>Não</option>
                                        <option value="3">3 Itens</option>
                                        <option value="5">5 Itens</option>
                                        <option value="7">7 Itens</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Comprimento do Artigo -->
                        <div class="article-settings-section">
                            <h4><i class="fas fa-text-height"></i> Comprimento do Artigo</h4>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="outlines_length" class="form-label">Comprimento dos Esboços:</label>
                                    <select class="form-select" id="outlines_length" name="outlines_length">
                                        <option value="short">Curto</option>
                                        <option value="medium" selected>Médio</option>
                                        <option value="long">Longo</option>
                                        <option value="extra_long">Extra Longo</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="intro_length" class="form-label">Comprimento da Introdução:</label>
                                    <select class="form-select" id="intro_length" name="intro_length">
                                        <option value="short" selected>Curto</option>
                                        <option value="medium">Médio</option>
                                        <option value="long">Longo</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="paragraphs_length" class="form-label">Comprimento dos Parágrafos:</label>
                                    <select class="form-select" id="paragraphs_length" name="paragraphs_length">
                                        <option value="short" selected>Curto</option>
                                        <option value="medium">Médio</option>
                                        <option value="long">Longo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="startGenerationBtn" disabled>
                                <i class="fas fa-play me-1"></i> Iniciar Geração
                            </button>
                            <div class="form-text text-center">
                                Após pressionar "Iniciar Geração", o processo começará automaticamente.
                                Você receberá uma notificação quando os artigos estiverem prontos.
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Resultados de Títulos -->
<div class="modal fade" id="titlesResultModal" tabindex="-1" aria-labelledby="titlesResultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titlesResultModalLabel">Ideias de Títulos Geradas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div id="titlesResultContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">Gerando ideias de títulos...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="addSelectedTitlesBtn">
                    <i class="fas fa-plus me-1"></i> Adicionar Selecionados
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Adicionar este botão para teste direto -->
<button type="button" id="testGenerateBtn" class="btn btn-warning mb-3">
    <i class="fas fa-bug me-1"></i> Testar Geração (Debug)
</button>
@endsection

@section('scripts')
<script>
    // Array para armazenar os títulos
    let titlesList = [];

    // Função para atualizar a lista de títulos
    function updateTitlesList() {
        const $titlesList = $('#titles-list');
        const $titlesCount = $('#titles-count');
        const $startGenerationBtn = $('#startGenerationBtn');
        const $clearTitlesBtn = $('#clearTitlesBtn');

        // Atualizar o contador
        $titlesCount.text(titlesList.length);

        // Atualizar o campo oculto com os títulos em JSON
        $('#titles-json').val(JSON.stringify(titlesList));

        // Habilitar/desabilitar botões
        $startGenerationBtn.prop('disabled', titlesList.length === 0);
        $clearTitlesBtn.prop('disabled', titlesList.length === 0);

        // Limpar e preencher a lista
        $titlesList.empty();

        if (titlesList.length === 0) {
            $titlesList.html(`
                <div class="text-center text-muted py-4">
                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                    <p>Adicione títulos usando as opções acima.</p>
                </div>
            `);
            return;
        }

        titlesList.forEach((title, index) => {
            $titlesList.append(`
                <div class="title-item">
                    <div class="title-text">${title}</div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-title" data-index="${index}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `);
        });

        // Adicionar evento para remover título
        $('.remove-title').on('click', function() {
            const index = $(this).data('index');
            titlesList.splice(index, 1);
            updateTitlesList();
        });
    }

    // Função para selecionar item de rádio
    function selectRadioItem(element) {
        // Remover a classe 'selected' de todos os itens no mesmo grupo
        $(element).siblings('.idea-radio-item').removeClass('selected');
        // Adicionar a classe 'selected' ao item clicado
        $(element).addClass('selected');
        // Marcar o input radio
        $(element).find('input[type="radio"]').prop('checked', true);
    }

    $(document).ready(function() {
        $('#generateForm').on('submit', function(e) {
            e.preventDefault();
            
            // Dados que serão enviados
            const data = {
                keywords: $('#keywords').val(),
                supportKeywords: $('#supportKeywords').val(),
                titleStyle: $('#titleStyle').val()
            };
            
            // Log dos dados
            console.log('Enviando dados:', data);
            
            // Mostrar loading
            $('#titlesResultContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Gerando títulos...</div>');
            $('#titlesResult').show();
            
            // Fazer a requisição AJAX
            $.ajax({
                url: '/api/generate-titles',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(data),
                dataType: 'json',
                success: function(response) {
                    console.log('Resposta recebida:', response);
                    
                    if (response.success) {
                        let titlesHTML = '<div class="list-group mt-3">';
                        response.titles.forEach(function(title) {
                            titlesHTML += `
                                <div class="list-group-item">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="${title}">
                                        <label class="form-check-label">${title}</label>
                                    </div>
                                </div>`;
                        });
                        titlesHTML += '</div>';
                        
                        $('#titlesResultContent').html(titlesHTML);
                    } else {
                        $('#titlesResultContent').html(`
                            <div class="alert alert-danger">
                                ${response.message || 'Erro ao gerar títulos'}
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    
                    let errorMessage = 'Erro ao gerar títulos';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        console.error('Erro ao parsear resposta:', e);
                    }
                    
                    $('#titlesResultContent').html(`
                        <div class="alert alert-danger">
                            ${errorMessage}
                        </div>
                    `);
                }
            });
        });
        
        // Adicionar títulos selecionados à lista
        $('#addSelectedTitlesBtn').on('click', function() {
            const selectedTitles = [];
            $('.title-checkbox:checked').each(function() {
                selectedTitles.push($(this).val());
            });
            
            if (selectedTitles.length === 0) {
                alert('Por favor, selecione pelo menos um título.');
                return;
            }
            
            titlesList = [...titlesList, ...selectedTitles];
            updateTitlesList();
            
            // Fechar o modal
            const titlesResultModal = bootstrap.Modal.getInstance(document.getElementById('titlesResultModal'));
            titlesResultModal.hide();
        });
        
        // Adicionar título manualmente
        $('#addManualTitlesBtn').on('click', function() {
            const manualTitle = $('#manual-title').val().trim();
            
            if (manualTitle.length < 10) {
                alert('O título deve ter pelo menos 10 caracteres.');
                return;
            }
            
            titlesList.push(manualTitle);
            updateTitlesList();
            
            // Limpar o campo
            $('#manual-title').val('');
        });
        
        // Limpar todos os títulos
        $('#clearTitlesBtn').on('click', function() {
            if (confirm('Tem certeza que deseja remover todos os títulos?')) {
                titlesList = [];
                updateTitlesList();
            }
        });
        
        // Iniciar geração
        $('#bulkGenerationForm').on('submit', function(e) {
            if (titlesList.length === 0) {
                e.preventDefault();
                alert('Por favor, adicione pelo menos um título à lista.');
                return;
            }
        });
        
        // Inicializar a lista de títulos
        updateTitlesList();
        
        // Inicializar os elementos de rádio
        $('.idea-radio-item').on('click', function() {
            selectRadioItem(this);
        });

        // Adicionar este código ao final do script
        $('#testGenerateBtn').on('click', function() {
            // Dados de teste
            const testData = {
                keywords: 'marketing digital',
                supportKeywords: 'redes sociais, SEO',
                titleStyle: 'question'
            };
            
            // Mostrar dados que serão enviados
            console.log('Enviando dados:', testData);
            
            // Fazer a chamada AJAX direta para teste
            $.ajax({
                url: '/api/generate-titles',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: testData,
                success: function(response) {
                    console.log('Resposta de sucesso:', response);
                    alert('Teste bem-sucedido! Verifique o console.');
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    
                    let errorMessage = 'Erro ao gerar títulos';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        console.error('Erro ao parsear resposta:', e);
                    }
                    
                    alert(`${errorMessage}. Verifique o console para mais detalhes.`);
                }
            });
        });
    });
</script>
@endsection