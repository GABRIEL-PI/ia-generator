@extends('layouts.app')

@section('title', 'Conectar WordPress')

@section('content')
    <div class="content-header">
        <h1><i class="fab fa-wordpress me-2"></i>Conectar Site WordPress</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('wordpress.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome do Site</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Um nome para identificar este site (ex: "Meu Blog").</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="url" class="form-label">URL do WordPress</label>
                            <input type="url" class="form-control @error('url') is-invalid @enderror" 
                                id="url" name="url" value="{{ old('url') }}" placeholder="https://seusite.com.br" required>
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">A URL completa do seu site WordPress.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Nome de Usuário</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                id="username" name="username" value="{{ old('username') }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">O nome de usuário de um administrador do WordPress.</div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle me-2"></i>Instruções</h5>
                            <p>Para conectar seu site WordPress, siga estes passos:</p>
                            <ol>
                                <li>Instale o plugin <strong>API Connector</strong> no seu WordPress</li>
                                <li>Preencha os campos acima e clique em "Conectar Site"</li>
                                <li>Copie o token gerado</li>
                                <li>No WordPress, vá para "API Connector" no menu lateral</li>
                                <li>Cole o token no campo "Token de API" e salve</li>
                            </ol>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plug me-1"></i> Conectar Site
                            </button>
                            <a href="{{ route('wordpress.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Voltar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 