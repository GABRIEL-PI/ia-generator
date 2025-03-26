@extends('layouts.app')

@section('title', 'Nova Conexão')

@section('content')
<div class="content-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Nova Conexão</h1>
        <a href="{{ route('connections.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('connections.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Nome da Conexão <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                    id="name" name="name" value="{{ old('name') }}" required
                    placeholder="Ex: Minha Automação Make">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="type" class="form-label">Tipo de Serviço <span class="text-danger">*</span></label>
                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                    <option value="">Selecione um serviço</option>
                    <option value="make" {{ old('type') == 'make' ? 'selected' : '' }}>Make.com</option>
                    <option value="n8n" {{ old('type') == 'n8n' ? 'selected' : '' }}>n8n</option>
                    <option value="typebot" {{ old('type') == 'typebot' ? 'selected' : '' }}>Typebot</option>
                    <option value="zapier" {{ old('type') == 'zapier' ? 'selected' : '' }}>Zapier</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="webhook_url" class="form-label">URL do Webhook <span class="text-danger">*</span></label>
                <input type="url" class="form-control @error('webhook_url') is-invalid @enderror" 
                    id="webhook_url" name="webhook_url" value="{{ old('webhook_url') }}" required
                    placeholder="https://...">
                @error('webhook_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">URL para onde os dados serão enviados quando um post for gerado.</div>
            </div>
            
            <div class="mb-3">
                <label for="api_key" class="form-label">Chave de API (opcional)</label>
                <input type="text" class="form-control @error('api_key') is-invalid @enderror" 
                    id="api_key" name="api_key" value="{{ old('api_key') }}"
                    placeholder="Chave de autenticação (se necessário)">
                @error('api_key')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Se o serviço exigir autenticação, forneça a chave de API aqui.</div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Salvar Conexão
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 