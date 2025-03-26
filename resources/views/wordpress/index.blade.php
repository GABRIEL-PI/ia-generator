@extends('layouts.app')

@section('title', 'Sites WordPress')

@section('content')
    <div class="content-header d-flex justify-content-between align-items-center">
        <h1><i class="fab fa-wordpress me-2"></i>Sites WordPress</h1>
        <a href="{{ route('wordpress.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Conectar Novo Site
        </a>
    </div>

    @if(session('generated_token'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <h4 class="alert-heading">Token de API Gerado!</h4>
        <p>Copie este token e cole-o no plugin API Connector no seu site WordPress:</p>
        <div class="input-group mb-3">
            <input type="text" class="form-control font-monospace" id="api-token" value="{{ session('generated_token') }}" readonly>
            <button class="btn btn-outline-secondary" type="button" id="copy-token-btn">
                <i class="fas fa-copy"></i> Copiar
            </button>
        </div>
        <p class="mb-0"><strong>Importante:</strong> Este token só será exibido uma vez. Guarde-o em um local seguro.</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($sites->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Você ainda não tem sites WordPress conectados. 
            <a href="{{ route('wordpress.create') }}" class="alert-link">Conecte seu primeiro site</a>.
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>URL</th>
                                <th>Usuário</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sites as $site)
                            <tr>
                                <td>{{ $site->name }}</td>
                                <td><a href="{{ $site->url }}" target="_blank">{{ $site->url }}</a></td>
                                <td>{{ $site->username }}</td>
                                <td>
                                    <span class="badge bg-secondary site-status" data-site-id="{{ $site->id }}">
                                        Verificar
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-primary test-connection" data-site-id="{{ $site->id }}">
                                            <i class="fas fa-sync-alt"></i> Testar
                                        </button>
                                        <form action="{{ route('wordpress.destroy', $site) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este site?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Copiar token para a área de transferência
        $('#copy-token-btn').on('click', function() {
            var tokenField = document.getElementById('api-token');
            tokenField.select();
            document.execCommand('copy');
            $(this).html('<i class="fas fa-check"></i> Copiado!');
            setTimeout(function() {
                $('#copy-token-btn').html('<i class="fas fa-copy"></i> Copiar');
            }, 2000);
        });
        
        // Testar conexão
        $('.test-connection').on('click', function() {
            var siteId = $(this).data('site-id');
            var badge = $('.site-status[data-site-id="' + siteId + '"]');
            
            badge.removeClass('bg-success bg-danger').addClass('bg-secondary').text('Verificando...');
            
            $.ajax({
                url: `/wordpress/${siteId}/test`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    badge.removeClass('bg-secondary bg-danger').addClass('bg-success').text('Conectado');
                },
                error: function(xhr) {
                    let message = 'Falha na conexão';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    badge.removeClass('bg-secondary bg-success').addClass('bg-danger').text('Erro');
                    badge.attr('title', message);
                }
            });
        });
    });
</script>
@endsection 