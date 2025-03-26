@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ $project->name }}</h1>
    <div>
        <div>
            <a href="{{ route('projects.bulk-generate') }}" class="btn btn-success me-2">
                <i class="fas fa-magic me-1"></i> Geração em Massa
            </a>
            <a href="{{ route('projects.create-post', $project) }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Novo Post
            </a>
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Detalhes do Projeto</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Site WordPress:</strong>
                        <a href="{{ $project->wordPressSite->url }}" target="_blank">
                            {{ $project->wordPressSite->name }}
                        </a>
                    </p>
                    <p><strong>Descrição:</strong> {{ $project->description ?: 'Nenhuma descrição fornecida' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Criado em:</strong> {{ $project->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Última atualização:</strong> {{ $project->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($project->posts->isEmpty())
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i> Este projeto ainda não tem posts. Clique em "Novo Post" para começar a gerar conteúdo.
    </div>
    @else
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Posts Gerados</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Créditos</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($project->posts as $post)
                    <tr>
                        <td>{{ $post->title }}</td>
                        <td>
                            @if($post->status == 'published')
                            <span class="badge bg-success">Publicado</span>
                            @elseif($post->status == 'draft')
                            <span class="badge bg-warning text-dark">Rascunho</span>
                            @else
                            <span class="badge bg-secondary">{{ $post->status }}</span>
                            @endif
                        </td>
                        <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $post->credits_used ?? 1 }}</td>
                        <td>
                            <a href="{{ route('posts.preview', $post->id) }}" class="btn btn-sm btn-info" title="Ver Post">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($post->wordpress_id)
                            <a href="{{ $post->wordpress_url }}"
                                target="_blank" class="btn btn-sm btn-primary" title="Ver no WordPress">
                                <i class="fab fa-wordpress"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endsection