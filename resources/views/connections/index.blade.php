@extends('layouts.app')

@section('title', 'Conexões')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Conexões</h1>
        <a href="{{ route('connections.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nova Conexão
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($connections->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-plug fa-3x mb-3 text-muted"></i>
                <h4>Nenhuma conexão configurada</h4>
                <p class="text-muted">Crie uma conexão para integrar com serviços externos como Make.com, n8n, Typebot ou Zapier.</p>
                <a href="{{ route('connections.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-1"></i> Criar Primeira Conexão
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>URL do Webhook</th>
                            <th>Criado em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($connections as $connection)
                        <tr>
                            <td>{{ $connection->name }}</td>
                            <td>
                                @switch($connection->type)
                                    @case('make')
                                        <span class="badge bg-primary">Make.com</span>
                                        @break
                                    @case('n8n')
                                        <span class="badge bg-success">n8n</span>
                                        @break
                                    @case('typebot')
                                        <span class="badge bg-info">Typebot</span>
                                        @break
                                    @case('zapier')
                                        <span class="badge bg-warning">Zapier</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $connection->type }}</span>
                                @endswitch
                            </td>
                            <td>{{ Str::limit($connection->webhook_url, 30) }}</td>
                            <td>{{ $connection->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('connections.edit', $connection) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('connections.destroy', $connection) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja excluir esta conexão?')">
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
        @endif
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Sobre Conexões</h5>
    </div>
    <div class="card-body">
        <p>As conexões permitem que você integre o IA Generator com serviços externos de automação, como:</p>
        
        <div class="row mt-4">
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="https://www.make.com/en/logo.svg" alt="Make.com" class="img-fluid mb-3" style="max-height: 40px;">
                        <h5>Make.com</h5>
                        <p class="text-muted">Crie fluxos de trabalho visuais para automatizar processos.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="https://n8n.io/images/n8n-logo.svg" alt="n8n" class="img-fluid mb-3" style="max-height: 40px;">
                        <h5>n8n</h5>
                        <p class="text-muted">Plataforma de automação de código aberto e baseada em nós.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="https://www.typebot.io/images/logo.svg" alt="Typebot" class="img-fluid mb-3" style="max-height: 40px;">
                        <h5>Typebot</h5>
                        <p class="text-muted">Crie chatbots conversacionais sem código.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="https://zapier-images.imgix.net/zapier/zapier-logo.svg" alt="Zapier" class="img-fluid mb-3" style="max-height: 40px;">
                        <h5>Zapier</h5>
                        <p class="text-muted">Conecte aplicativos e automatize fluxos de trabalho.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 