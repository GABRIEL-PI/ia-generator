@extends('layouts.app')

@section('title', 'Geração em Massa')

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
        <h1><i class="fas fa-magic me-2"></i>Geração em Massa</h1>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="generationTabs" role="tablist">
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
                            <button class="nav-link" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual-content" type="button" role="tab" aria-controls="manual-content" aria-selected="false">
                                <i class="fas fa-edit me-1"></i> Manual
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="generationTabsContent">
                        <!-- Aba de Palavras-chave -->
                        <div class="tab-pane fade show active" id="keywords-content" role="tabpanel" aria-labelledby="keywords-tab">
                            <form id="keywordsForm">
                                <div class="mb-3">
                                    <label for="keywords" class="form-label">Palavras-chave Principais</label>
                                    <input type="text" class="form-control" id="keywords" name="keywords" placeholder="Ex: Refinanciar, Veículo, Economizar, Dinheiro">
                                    <div class="form-text">Separe as palavras-chave por vírgulas</div>
                                </div>
                                <div class="mb-3">
                                    <label for="supportKeywords" class="form-label">Palavras-chave de Suporte (opcional)</label>
                                    <input type="text" class="form-control" id="supportKeywords" name="supportKeywords" placeholder="Ex: Wireless earbuds, sport, waterproof, brand: Philips">
                                    <div class="form-text">Palavras-chave secundárias para enriquecer o conteúdo</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Estilo do Título</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="titleStyle" id="styleQuestion" value="question" checked>
                                        <label class="form-check-label" for="styleQuestion">Pergunta</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="titleStyle" id="styleGuide" value="guide">
                                        <label class="form-check-label" for="styleGuide">Guia</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="titleStyle" id="styleEvergreen" value="evergreen">
                                        <label class="form-check-label" for="styleEvergreen">Evergreen</label>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-dark" id="generateTitles">
                                    <i class="fas fa-bolt me-1"></i> GERAR IDEIAS
                                </button>
                            </form>
                        </div>
                        
                        <!-- Aba de Títulos -->
                        <div class="tab-pane fade" id="titles-content" role="tabpanel" aria-labelledby="titles-tab">
                            <div id="titlesResultContent">
                                <div id="titlesList" class="list-group">
                                    <!-- Títulos gerados serão exibidos aqui -->
                                </div>
                                <div id="emptyTitlesMessage" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle me-1"></i> Nenhum título gerado ainda. Use a aba "Palavras-chave" para gerar títulos.
                                </div>
                            </div>
                        </div>
                        
                        <!-- Aba Manual -->
                        <div class="tab-pane fade" id="manual-content" role="tabpanel" aria-labelledby="manual-tab">
                            <div class="mb-3">
                                <label for="manualTitle" class="form-label">Adicionar Título Manualmente</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="manualTitle" placeholder="Digite um título">
                                    <button class="btn btn-outline-secondary" type="button" id="addManualTitle">
                                        <i class="fas fa-plus"></i> Adicionar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lista de Títulos Selecionados -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-1"></i> Fila de Artigos para Geração
                        <span class="badge bg-primary ms-2" id="titleCount">0</span>
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="clearTitles">
                        <i class="fas fa-trash me-1"></i> Limpar
                    </button>
                </div>
                <div class="card-body">
                    <div id="selectedTitlesList" class="list-group">
                        <!-- Títulos selecionados serão exibidos aqui -->
                    </div>
                    <div id="emptySelectedMessage" class="text-center py-4 text-muted">
                        <i class="fas fa-info-circle me-1"></i> Nenhum título selecionado. Gere ou adicione títulos para começar.
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Configurações de Geração -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cog me-1"></i> Configurações de Geração</h5>
                </div>
                <div class="card-body">
                    <form id="bulkGenerationForm" action="{{ route('projects.bulk-generate.store') }}" method="POST">
                        @csrf
                        
                        <!-- Campo oculto para armazenar os títulos -->
                        <input type="hidden" name="titles" id="titlesInput">
                        
                        <div class="mb-3">
                            <label class="form-label">Estilo do Artigo</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="article_style" id="styleInformative" value="informative" checked>
                                <label class="form-check-label" for="styleInformative">Informativo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="article_style" id="styleGuide" value="guide">
                                <label class="form-check-label" for="styleGuide">Guia</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="article_style" id="styleTutorial" value="tutorial">
                                <label class="form-check-label" for="styleTutorial">Tutorial</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="language" class="form-label">Idioma</label>
                            <select class="form-select" id="language" name="language">
                                <option value="pt-br" selected>Português (Brasil)</option>
                                <option value="en-us">Inglês (EUA)</option>
                                <option value="es">Espanhol</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="pov" class="form-label">Ponto de Vista</label>
                            <select class="form-select" id="pov" name="pov">
                                <option value="first">Primeira Pessoa (Eu, Nós)</option>
                                <option value="second" selected>Segunda Pessoa (Você, Vocês)</option>
                                <option value="third">Terceira Pessoa (Ele, Ela, Eles)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tone" class="form-label">Tom</label>
                            <select class="form-select" id="tone" name="tone">
                                <option value="informative" selected>Informativo</option>
                                <option value="conversational">Conversacional</option>
                                <option value="professional">Profissional</option>
                                <option value="friendly">Amigável</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="words" class="form-label">Número de Palavras</label>
                            <select class="form-select" id="words" name="words">
                                <option value="500">500 palavras</option>
                                <option value="1000">1000 palavras</option>
                                <option value="1500" selected>1500 palavras</option>
                                <option value="2000">2000 palavras</option>
                                <option value="2500">2500 palavras</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="faq" class="form-label">Adicionar FAQ</label>
                            <select class="form-select" id="faq" name="faq">
                                <option value="0">Não adicionar</option>
                                <option value="3">3 perguntas</option>
                                <option value="5" selected>5 perguntas</option>
                                <option value="7">7 perguntas</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="key_takeaways" class="form-label">Pontos-chave</label>
                            <select class="form-select" id="key_takeaways" name="key_takeaways">
                                <option value="0">Não adicionar</option>
                                <option value="3" selected>3 itens</option>
                                <option value="5">5 itens</option>
                                <option value="7">7 itens</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="outlines_length" class="form-label">Comprimento dos Esboços</label>
                            <select class="form-select" id="outlines_length" name="outlines_length">
                                <option value="short">Curto</option>
                                <option value="medium" selected>Médio</option>
                                <option value="long">Longo</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="intro_length" class="form-label">Comprimento da Introdução</label>
                            <select class="form-select" id="intro_length" name="intro_length">
                                <option value="short" selected>Curto</option>
                                <option value="medium">Médio</option>
                                <option value="long">Longo</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="paragraphs_length" class="form-label">Comprimento dos Parágrafos</label>
                            <select class="form-select" id="paragraphs_length" name="paragraphs_length">
                                <option value="short" selected>Curto</option>
                                <option value="medium">Médio</option>
                                <option value="long">Longo</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="startGenerationBtn">
                                <i class="fas fa-play me-1"></i> Iniciar Geração
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Array para armazenar os títulos selecionados
    let selectedTitles = [];
    
    // Função para atualizar a contagem de títulos
    function updateTitleCount() {
        $('#titleCount').text(selectedTitles.length);
        
        // Atualizar o campo oculto com os títulos
        $('#titlesInput').val(JSON.stringify(selectedTitles));
        
        // Mostrar/ocultar mensagem de lista vazia
        if (selectedTitles.length > 0) {
            $('#emptySelectedMessage').hide();
            $('#startGenerationBtn').prop('disabled', false);
        } else {
            $('#emptySelectedMessage').show();
            $('#startGenerationBtn').prop('disabled', true);
        }
    }
    
    // Função para adicionar um título à lista de selecionados
    function addTitleToSelected(title) {
        // Verificar se o título já existe
        if (selectedTitles.includes(title)) {
            alert('Este título já está na lista.');
            return;
        }
        
        // Adicionar ao array
        selectedTitles.push(title);
        
        // Criar elemento HTML
        const titleId = 'selected-' + Date.now();
        const titleElement = `
            <div class="list-group-item d-flex justify-content-between align-items-center" id="${titleId}">
                <span>${title}</span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-title" data-id="${titleId}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        // Adicionar à lista
        $('#selectedTitlesList').append(titleElement);
        
        // Atualizar contagem
        updateTitleCount();
    }
    
    // Gerar títulos
    $('#generateTitles').click(function() {
        const keywords = $('#keywords').val();
        if (!keywords) {
            alert('Por favor, insira palavras-chave principais.');
            return;
        }
        
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Gerando...');
        
        // Limpar títulos anteriores
        $('#titlesList').empty();
        $('#emptyTitlesMessage').hide();
        
        $.ajax({
            url: '/api/generate-titles',
            type: 'POST',
            data: {
                keywords: keywords,
                supportKeywords: $('#supportKeywords').val(),
                titleStyle: $('input[name="titleStyle"]:checked').val()
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.titles) {
                    // Mudar para a aba de títulos
                    $('#titles-tab').tab('show');
                    
                    // Adicionar cada título à lista
                    response.titles.forEach(title => {
                        const titleId = 'title-' + Date.now() + Math.floor(Math.random() * 1000);
                        const titleElement = `
                            <div class="list-group-item d-flex justify-content-between align-items-center" id="${titleId}">
                                <span>${title}</span>
                                <button type="button" class="btn btn-sm btn-outline-primary add-title" data-title="${title.replace(/"/g, '&quot;')}">
                                    <i class="fas fa-plus"></i> Adicionar
                                </button>
                            </div>
                        `;
                        $('#titlesList').append(titleElement);
                        
                        // Adicionar automaticamente à lista de selecionados
                        addTitleToSelected(title);
                    });
                } else {
                    $('#titlesList').html('<div class="alert alert-warning">Nenhum título foi gerado. Tente outras palavras-chave.</div>');
                }
            },
            error: function(xhr) {
                $('#titlesList').html('<div class="alert alert-danger">Erro ao gerar títulos: ' + (xhr.responseJSON?.message || 'Erro desconhecido') + '</div>');
            },
            complete: function() {
                $('#generateTitles').prop('disabled', false).html('<i class="fas fa-bolt me-1"></i> GERAR IDEIAS');
            }
        });
    });
    
    // Adicionar título manualmente
    $('#addManualTitle').click(function() {
        const title = $('#manualTitle').val().trim();
        if (title) {
            addTitleToSelected(title);
            $('#manualTitle').val('');
        } else {
            alert('Por favor, digite um título.');
        }
    });
    
    // Permitir pressionar Enter para adicionar título manualmente
    $('#manualTitle').keypress(function(e) {
        if (e.which === 13) {
            $('#addManualTitle').click();
            return false;
        }
    });
    
    // Adicionar título da lista de gerados (usando delegação de eventos)
    $(document).on('click', '.add-title', function() {
        const title = $(this).data('title');
        addTitleToSelected(title);
    });
    
    // Remover título da lista de selecionados (usando delegação de eventos)
    $(document).on('click', '.remove-title', function() {
        const titleId = $(this).data('id');
        const titleText = $('#' + titleId + ' span').text();
        
        // Remover do array
        selectedTitles = selectedTitles.filter(title => title !== titleText);
        
        // Remover do DOM
        $('#' + titleId).remove();
        
        // Atualizar contagem
        updateTitleCount();
    });
    
    // Limpar todos os títulos
    $('#clearTitles').click(function() {
        if (selectedTitles.length > 0 && confirm('Tem certeza que deseja remover todos os títulos?')) {
            selectedTitles = [];
            $('#selectedTitlesList').empty();
            updateTitleCount();
        }
    });
    
    // Função para atualizar o campo oculto com os títulos selecionados
    function updateTitlesInput() {
        // Obter todos os títulos selecionados
        const titles = $('.title-checkbox:checked').map(function() {
            return this.value;
        }).get();
        
        // Atualizar o campo oculto
        $('#titlesInput').val(JSON.stringify(titles));
        
        // Habilitar/desabilitar o botão de geração
        $('#startGenerationBtn').prop('disabled', titles.length === 0);
    }
    
    // Atualizar quando os checkboxes forem alterados
    $(document).on('change', '.title-checkbox', updateTitlesInput);
    
    // Atualizar inicialmente
    updateTitlesInput();
    
    // Validar o formulário antes de enviar
    $('#bulkGenerationForm').on('submit', function(e) {
        const titles = JSON.parse($('#titlesInput').val() || '[]');
        
        if (titles.length === 0) {
            e.preventDefault();
            alert('Por favor, selecione pelo menos um título antes de iniciar a geração.');
            return false;
        }
    });
});
</script>
@endsection