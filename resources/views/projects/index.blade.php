@extends('layouts.app')

@section('title', 'Meus Projetos')

@section('content')
    <div class="content-header d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-project-diagram me-2"></i>Meus Projetos</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Novo Projeto
        </a>
    </div>

    @if($projects->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Você ainda não tem projetos. 
            <a href="{{ route('projects.create') }}" class="alert-link">Crie seu primeiro projeto</a>.
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($projects as $project)
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $project->title }}</h5>
                            <p class="card-text text-muted">
                                <i class="fab fa-wordpress me-1"></i> {{ $project->wordPressSite->name }}
                            </p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt me-1"></i> Criado em {{ $project->created_at->format('d/m/Y') }}
                                </small>
                            </p>
                            <p class="card-text">
                                <span class="badge bg-info">{{ $project->posts->count() }} posts</span>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-primary w-100">
                                <i class="fas fa-eye me-1"></i> Ver Projeto
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection 