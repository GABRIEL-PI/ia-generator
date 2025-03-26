@extends('layouts.app')

@section('title', 'Criar Novo Projeto')

@section('content')
    <div class="content-header">
        <h1><i class="fas fa-plus-circle me-2"></i>Criar Novo Projeto</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if($wordPressSites->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Você precisa conectar um site WordPress antes de criar um projeto.
                            <a href="{{ route('wordpress.create') }}" class="alert-link">Conectar WordPress</a>
                        </div>
                    @else
                        <form action="{{ route('projects.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Título do Projeto</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                    id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="wordpress_site_id" class="form-label">Site WordPress</label>
                                <select class="form-select @error('wordpress_site_id') is-invalid @enderror" 
                                    id="wordpress_site_id" name="wordpress_site_id" required>
                                    <option value="">Selecione um site WordPress</option>
                                    @foreach($wordPressSites as $site)
                                        <option value="{{ $site->id }}" {{ old('wordpress_site_id') == $site->id ? 'selected' : '' }}>
                                            {{ $site->name }} ({{ $site->url }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('wordpress_site_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Criar Projeto
                                </button>
                                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Voltar
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 