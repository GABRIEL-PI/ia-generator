@extends('layouts.app')

@section('title', 'Visualizar Post')

@section('styles')
<style>
    .post-preview-container {
        display: flex;
        height: calc(100vh - 120px);
    }
    
    .post-list-sidebar {
        width: 30%;
        border-right: 1px solid var(--border-color);
        padding: 15px;
        overflow-y: auto;
    }
    
    .post-content-area {
        width: 70%;
        display: flex;
        flex-direction: column;
    }
    
    .post-tabs {
        display: flex;
        border-bottom: 1px solid var(--border-color);
    }
    
    .post-tab {
        padding: 10px 20px;
        cursor: pointer;
        border-bottom: 3px solid transparent;
    }
    
    .post-tab.active {
        border-bottom-color: var(--primary-color);
        font-weight: bold;
    }
    
    .post-content {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto;
    }
    
    .post-editor {
        height: 100%;
    }
    
    .post-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        border-top: 1px solid var(--border-color);
    }
    
    .post-item {
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .post-item:hover {
        background-color: rgba(67, 72, 215, 0.1);
    }
    
    .post-item.active {
        background-color: rgba(67, 72, 215, 0.2);
    }
    
    .post-item-title {
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .post-item-meta {
        font-size: 0.8rem;
        color: #666;
    }
    
    .word-count {
        color: var(--text-color);
        font-size: 0.9rem;
    }
    
    .publish-options {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 5px;
        padding: 15px;
        margin-top: 15px;
    }
    
    .ai-images-container {
        padding: 20px;
        display: none;
    }
    
    .ai-images-container ul {
        list-style: none;
        padding: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .ai-images-container li {
        width: calc(33.333% - 10px);
        position: relative;
    }
    
    .ai-images-container img {
        width: 100%;
        height: auto;
        border-radius: 5px;
        border: 1px solid var(--border-color);
    }
    
    .image-actions {
        position: absolute;
        bottom: 5px;
        right: 5px;
        display: flex;
        gap: 5px;
    }
    
    .image-action-btn {
        background-color: rgba(255, 255, 255, 0.8);
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .image-action-btn:hover {
        background-color: rgba(255, 255, 255, 1);
    }
    
    .connection-tabs {
        display: flex;
        margin-bottom: 10px;
    }
    
    .conn-tab {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .conn-tab.active-tab {
        border-bottom-color: var(--primary-color);
        font-weight: bold;
    }
    
    .connect-tab-content {
        display: none;
    }
    
    .connect-tab-content.active {
        display: block;
    }
    
    /* Estilos para a lista de posts */
    .posts-list {
        margin-top: 20px;
    }
    
    .posts-list-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding: 0 10px;
    }
    
    .posts-list-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .posts-list-table th,
    .posts-list-table td {
        padding: 8px 10px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }
    
    .posts-list-table tr:hover {
        background-color: rgba(67, 72, 215, 0.05);
    }
    
    .post-actions-dropdown {
        position: relative;
        display: inline-block;
    }
    
    .post-actions-dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: var(--card-bg);
        min-width: 160px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        z-index: 1;
        border-radius: 4px;
        border: 1px solid var(--border-color);
    }
    
    .post-actions-dropdown-content a {
        color: var(--text-color);
        padding: 8px 12px;
        text-decoration: none;
        display: block;
        font-size: 14px;
    }
    
    .post-actions-dropdown-content a:hover {
        background-color: rgba(67, 72, 215, 0.1);
    }
    
    .post-actions-dropdown:hover .post-actions-dropdown-content {
        display: block;
    }
    
    /* Estilos para o agendamento */
    .schedule-dashboard {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 5px;
        padding: 15px;
    }
    
    .schedule-select-form {
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background-color: var(--input-bg);
        color: var(--text-color);
        margin-right: 10px;
    }
    
    /* Estilos para as conexões */
    .connection-tabs {
        display: flex;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 15px;
    }
    
    .conn-tab {
        padding: 10px 15px;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        margin-right: 10px;
        display: flex;
        align-items: center;
    }
    
    .conn-tab i, 
    .conn-tab .material-icons {
        margin-right: 5px;
    }
    
    .conn-tab.active-tab {
        border-bottom-color: var(--primary-color);
        font-weight: bold;
    }
    
    .connect-tab-content {
        padding: 15px 0;
    }
    
    .radio-item {
        display: inline-block;
        margin-right: 15px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('projects.show', $post->project) }}" class="btn btn-sm btn-dark">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <a href="{{ route('projects.show', $post->project) }}" class="btn btn-sm btn-dark ms-1">
                            <i class="fas fa-list"></i> TODOS OS ARTIGOS
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('projects.create-post', $post->project) }}" class="btn btn-sm btn-dark">
                            <i class="fas fa-plus"></i> GERADOR EM MASSA
                        </a>
                        <a href="{{ route('projects.create-post', $post->project) }}" class="btn btn-sm btn-dark ms-1">
                            <i class="fas fa-edit"></i> ARTIGO ÚNICO
                        </a>
                    </div>
                </div>
                
                <!-- Painel de Agendamento -->
                <div class="schedule-dashboard mx-3 my-3">
                    <form action="{{ route('posts.schedule') }}" method="post" class="d-flex flex-column">
                        @csrf
                        <input name="project_id" value="{{ $post->project_id }}" type="hidden">
                        
                        <div class="d-flex justify-content-between mb-2">
                            <div><span id="resetSchedule" class="text-primary" style="cursor: pointer;">RESET SCHEDULE</span></div>
                        </div>
                        
                        <div class="d-flex mb-2">
                            <select class="schedule-select-form" name="wordpress_site_id" style="flex: 1;">
                                <option value="0">Selecionar WordPress</option>
                                @foreach($wordPressSites as $site)
                                    <option value="{{ $site->id }}" {{ $post->project->wordpress_site_id == $site->id ? 'selected' : '' }}>
                                        {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select class="schedule-select-form" name="daily_articles" style="flex: 1;">
                                <option value="0">Frequência</option>
                                <option value="1">1 Artigo Diário</option>
                                <option value="2">2-3 Artigos Diários</option>
                                <option value="3">3-5 Artigos Diários</option>
                                <option value="4">5-10 Artigos Diários</option>
                                <option value="5">10-20 Artigos Diários</option>
                            </select>
                        </div>
                        
                        <div>
                            <button type="submit" class="btn btn-dark" id="schedulebutton" title="Agendar publicação automática">
                                <i class="fas fa-sync-alt me-1"></i> AGENDAR TODOS
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Lista de Posts -->
                <div class="posts-list mx-3 mb-3">
                    <div class="posts-list-header">
                        <div>
                            <input type="checkbox" id="selectAll"> Selecionar Todos
                        </div>
                        <div id="WPMiniForm" style="display: none;">
                            <div class="post-to-wordpress miniform">
                                <select class="schedule-select-form" name="miniform_wp_id" id="WPMiniForm_wp_id">
                                    <option value="0" selected></option>
                                    @foreach($wordPressSites as $site)
                                        <option value="{{ $site->id }}">{{ $site->name }}</option>
                                    @endforeach
                                </select>
                                <div class="radio-item">
                                    <label class="form-label"><b>Publicar</b></label>
                                    <input type="radio" name="miniform_publish_status" value="1" checked>
                                </div>
                                <div class="radio-item">
                                    <label class="form-label"><b>Rascunho</b></label>
                                    <input type="radio" name="miniform_publish_status" value="2">
                                </div>
                                <button id="submitBtn" class="btn btn-primary">PUBLICAR</button>
                                <div class="close-miniform" id="closeMiniform">X</div>
                            </div>
                        </div>
                    </div>
                    
                    <table class="posts-list-table">
                        <tbody>
                            @foreach($post->project->posts as $projectPost)
                                <tr>
                                    <td width="30"><input type="checkbox" class="post-checkbox" value="{{ $projectPost->id }}"></td>
                                    <td>
                                        <a href="{{ route('posts.preview', $projectPost->id) }}" class="{{ $projectPost->id == $post->id ? 'fw-bold' : '' }}">
                                            {{ $projectPost->title }}
                                        </a>
                                    </td>
                                    <td width="100">{{ $projectPost->created_at->format('d M') }}</td>
                                    <td width="80">
                                        <div class="d-flex">
                                            <a href="{{ route('posts.preview', $projectPost->id) }}" class="btn btn-sm btn-outline-secondary me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <!-- Paginação -->
                    <nav class="mt-3">
                        <ul class="pagination">
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex">
                        <div class="post-tab active" id="textTab">Texto</div>
                        <div class="post-tab" id="imagesTab">Imagens IA</div>
                        <div class="ms-auto">
                            <input type="text" id="shareLink" value="{{ route('posts.preview', $post->id) }}" class="form-control form-control-sm d-inline-block" style="width: 250px;" readonly>
                            <button class="btn btn-sm btn-outline-secondary" onclick="copyShareLink()">
                                <i class="fas fa-share-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <!-- Conteúdo do texto -->
                    <div id="textContent" class="post-content">
                        <textarea id="postEditor">{{ $post->content }}</textarea>
                    </div>
                    
                    <!-- Conteúdo das imagens -->
                    <div id="imagesContent" class="ai-images-container" style="display: none;">
                        <div class="mb-3">
                            <button id="generateImagesBtn" class="btn btn-primary">
                                <i class="fas fa-image me-1"></i> Gerar Imagens com IA
                            </button>
                        </div>
                        <ul id="aiImagesList"></ul>
                    </div>
                </div>
                
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="post-to-wordpress me-3">
                            <div class="connection-tabs">
                                <span class="conn-tab active-tab" data-tab-target="#conn_wordpress">
                                    <i class="fab fa-wordpress"></i> WordPress
                                </span>
                                
                                @if(isset($connections) && count($connections) > 0)
                                    @foreach($connections as $connection)
                                        <span class="conn-tab" data-tab-target="#conn_{{ $connection->type }}_{{ $connection->id }}">
                                            <i class="fas fa-{{ $connection->type == 'make' ? 'cogs' : 'plug' }}"></i> {{ $connection->name }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="conn-tab" data-tab-target="#conn_make">
                                        <i class="fas fa-cogs"></i> Make.com
                                    </span>
                                @endif
                            </div>
                            
                            <div class="connection-content">
                                <!-- WordPress -->
                                <div class="connect-tab-content" id="conn_wordpress">
                                    <form action="{{ route('posts.publish', $post->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="project_id" value="{{ $post->project_id }}">
                                        
                                        <select class="schedule-select-form" name="wordpress_site_id">
                                            <option value="0"></option>
                                            @foreach($wordPressSites as $site)
                                                <option value="{{ $site->id }}" {{ $post->project->wordpress_site_id == $site->id ? 'selected' : '' }}>
                                                    {{ $site->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        
                                        <div class="radio-item">
                                            <label class="form-label"><b>Publicar</b></label>
                                            <input type="radio" name="status" value="publish" checked>
                                        </div>
                                        <div class="radio-item">
                                            <label class="form-label"><b>Rascunho</b></label>
                                            <input type="radio" name="status" value="draft">
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">PUBLICAR</button>
                                    </form>
                                </div>
                                
                                <!-- Make.com e outras conexões -->
                                @if(isset($connections) && count($connections) > 0)
                                    @foreach($connections as $connection)
                                        <div class="connect-tab-content" id="conn_{{ $connection->type }}_{{ $connection->id }}" style="display: none;">
                                            <form action="{{ route('connections.publish', $post->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="project_id" value="{{ $post->project_id }}">
                                                <input type="hidden" name="connection_id" value="{{ $connection->id }}">
                                                
                                                <div class="radio-item">
                                                    <label class="form-label"><b>Publicar</b></label>
                                                    <input type="radio" name="status" value="publish" checked>
                                                </div>
                                                <div class="radio-item">
                                                    <label class="form-label"><b>Rascunho</b></label>
                                                    <input type="radio" name="status" value="draft">
                                                </div>
                                                
                                                <button type="submit" class="btn btn-primary">PUBLICAR</button>
                                            </form>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="connect-tab-content" id="conn_make" style="display: none;">
                                        <form action="{{ route('connections.publish', $post->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="project_id" value="{{ $post->project_id }}">
                                            
                                            <select class="schedule-select-form" name="connection_id">
                                                <option value="0"></option>
                                                <!-- Aqui você pode adicionar conexões de exemplo -->
                                                <option value="example">Exemplo de Conexão</option>
                                            </select>
                                            
                                            <div class="radio-item">
                                                <label class="form-label"><b>Publicar</b></label>
                                                <input type="radio" name="status" value="publish" checked>
                                            </div>
                                            <div class="radio-item">
                                                <label class="form-label"><b>Rascunho</b></label>
                                                <input type="radio" name="status" value="draft">
                                            </div>
                                            
                                            <button type="submit" class="btn btn-primary">PUBLICAR</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @if($post->wordpress_url)
                            <div class="wp-info">
                                Publicado em: {{ $post->updated_at->format('d.M') }} 
                                <a href="{{ $post->wordpress_url }}" target="_blank">VER POST</a>
                            </div>
                        @endif
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <button id="saveChangesBtn" class="btn btn-success me-2">
                            <i class="fas fa-save me-1"></i> Salvar
                        </button>
                        <div class="word-count">
                            Palavras: <span id="wordCountDisplay">{{ $wordCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.tiny.cloud/1/4ctih2gyra6hnij773f8ejxyn80rorf1vvrusz8soozp1r7f/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    $(document).ready(function() {
        tinymce.init({
            selector: '#postEditor',
            height: '100%',
            plugins: [
                'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
                'checklist', 'mediaembed', 'casechange', 'export', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'mentions', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown'
            ],
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | alignleft aligncenter alignright alignjustify | bullist numlist checklist | emoticons charmap | removeformat',
            content_css: false,
            verify_html: false,
            setup: function(editor) {
                editor.on('keyup', function() {
                    // Atualizar a contagem de palavras
                    const content = editor.getContent({format: 'text'});
                    const wordCount = content.split(/\s+/).filter(Boolean).length;
                    $('#wordCountDisplay').text(wordCount);
                });
            }
        });
        
        // Alternar entre as abas
        $('#textTab').click(function() {
            $('.post-tab').removeClass('active');
            $(this).addClass('active');
            $('#imagesContent').hide();
            $('#textContent').show();
        });
        
        $('#imagesTab').click(function() {
            $('.post-tab').removeClass('active');
            $(this).addClass('active');
            $('#textContent').hide();
            $('#imagesContent').show();
        });
        
        // Alternar entre as abas de conexão
        $('.conn-tab').click(function() {
            $('.conn-tab').removeClass('active-tab');
            $(this).addClass('active-tab');
            
            const target = $(this).data('tab-target');
            $('.connect-tab-content').hide();
            $(target).show();
        });
        
        // Salvar alterações
        $('#saveChangesBtn').click(function() {
            const content = tinymce.get('postEditor').getContent();
            
            // Enviar solicitação AJAX para salvar as alterações
            $.ajax({
                url: '/posts/{{ $post->id }}/update',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    content: content
                },
                success: function(response) {
                    if (response.success) {
                        alert('Alterações salvas com sucesso!');
                    } else {
                        alert('Erro ao salvar alterações: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Erro ao salvar alterações. Por favor, tente novamente.');
                }
            });
        });
        
        // Selecionar todos os posts
        $('#selectAll').change(function() {
            $('.post-checkbox').prop('checked', $(this).prop('checked'));
            
            if ($(this).prop('checked')) {
                $('#WPMiniForm').show();
            } else {
                $('#WPMiniForm').hide();
            }
        });
        
        // Mostrar mini-formulário quando algum checkbox for marcado
        $('.post-checkbox').change(function() {
            if ($('.post-checkbox:checked').length > 0) {
                $('#WPMiniForm').show();
            } else {
                $('#WPMiniForm').hide();
            }
        });
        
        // Fechar mini-formulário
        $('#closeMiniform').click(function() {
            $('#WPMiniForm').hide();
            $('.post-checkbox').prop('checked', false);
            $('#selectAll').prop('checked', false);
        });
        
        // Função para gerar imagens com a API DALL-E
        function generateImages() {
            $('#generateImagesBtn').html('<i class="fas fa-spinner fa-spin me-1"></i> Gerando imagens...');
            $('#generateImagesBtn').prop('disabled', true);
            
            $.ajax({
                url: '{{ route("posts.generate-images", $post->id) }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    prompt: 'Imagem para artigo sobre {{ addslashes($post->title) }}',
                    count: 6
                },
                success: function(response) {
                    if (response.success) {
                        let html = '';
                        response.images.forEach(function(url, index) {
                            html += `
                                <li>
                                    <img src="${url}" alt="Imagem gerada ${index + 1}">
                                    <div class="image-actions">
                                        <button class="image-action-btn insert-image" data-url="${url}" title="Inserir no texto">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button class="image-action-btn download-image" data-url="${url}" title="Baixar imagem">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </li>
                            `;
                        });
                        
                        $('#aiImagesList').html(html);
                        
                        // Adicionar eventos para os botões de ação das imagens
                        $('.insert-image').click(function() {
                            const imageUrl = $(this).data('url');
                            const imageHtml = `<p><img src="${imageUrl}" alt="Imagem gerada" style="max-width: 100%; height: auto;"></p>`;
                            tinymce.get('postEditor').execCommand('mceInsertContent', false, imageHtml);
                            $('#textTab').click(); // Mudar para a aba de texto
                        });
                        
                        $('.download-image').click(function() {
                            const imageUrl = $(this).data('url');
                            const link = document.createElement('a');
                            link.href = imageUrl;
                            link.download = 'imagem-ia-' + Date.now() + '.jpg';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        });
                    } else {
                        alert('Erro ao gerar imagens: ' + response.message);
                    }
                    
                    $('#generateImagesBtn').html('<i class="fas fa-image me-1"></i> Gerar Mais Imagens');
                    $('#generateImagesBtn').prop('disabled', false);
                },
                error: function(xhr) {
                    alert('Erro ao gerar imagens. Por favor, tente novamente.');
                    $('#generateImagesBtn').html('<i class="fas fa-image me-1"></i> Gerar Imagens');
                    $('#generateImagesBtn').prop('disabled', false);
                }
            });
        }
        
        // Chamar a função quando o botão for clicado
        $('#generateImagesBtn').click(function() {
            generateImages();
        });
        
        // Copiar link de compartilhamento
        window.copyShareLink = function() {
            const shareLink = document.getElementById('shareLink');
            shareLink.select();
            document.execCommand('copy');
            alert('Link copiado para a área de transferência!');
        };
        
        // Reset do agendamento
        $('#resetSchedule').click(function() {
            if (confirm('Tem certeza que deseja resetar o agendamento?')) {
                $.ajax({
                    url: '/posts/reset-schedule',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        project_id: '{{ $post->project_id }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Agendamento resetado com sucesso!');
                        } else {
                            alert('Erro ao resetar agendamento: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Erro ao resetar agendamento. Por favor, tente novamente.');
                    }
                });
            }
        });
    });
</script>
@endsection 