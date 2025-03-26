@php
use Illuminate\Support\Facades\Auth;
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>IA Generator - @yield('title', 'Gerador de Conteúdo com IA')</title>
    
    <!-- Script para aplicar o tema antes do carregamento da página -->
    <script>
        // Aplicar tema imediatamente para evitar piscar
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    </script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4348d7;
            --secondary-color: #b629ff;
            --light-bg: #f8f9fa;
            --dark-bg: #212529;
            --dark-text: #333;
            --light-text: #f8f9fa;
        }
        
        [data-bs-theme="light"] {
            --bg-color: var(--light-bg);
            --text-color: var(--dark-text);
            --card-bg: #fff;
            --border-color: #eee;
        }
        
        [data-bs-theme="dark"] {
            --bg-color: var(--dark-bg);
            --text-color: var(--light-text);
            --card-bg: #2c3034;
            --border-color: #444;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        
        .navbar {
            background: linear-gradient(76.35deg, var(--primary-color) 40.09%, var(--secondary-color) 125.4%);
        }
        
        .btn-primary {
            background: linear-gradient(76.35deg, var(--primary-color) 40.09%, var(--secondary-color) 125.4%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(76.35deg, var(--primary-color) 50.09%, var(--secondary-color) 135.4%);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, background-color 0.3s;
            background-color: var(--card-bg);
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .sidebar {
            background-color: var(--card-bg);
            border-right: 1px solid var(--border-color);
            min-height: calc(100vh - 56px);
            transition: background-color 0.3s;
        }
        
        .sidebar .nav-link {
            color: var(--text-color);
            padding: 10px 15px;
            border-radius: 5px;
            margin: 5px 0;
            transition: background-color 0.3s, color 0.3s;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(67, 72, 215, 0.1);
            color: var(--primary-color);
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .content-header {
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        
        .template-card {
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .template-card.selected {
            border-color: var(--primary-color);
            background-color: rgba(67, 72, 215, 0.05);
        }
        
        .theme-switch {
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .theme-switch:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        /* Animação para o ícone de tema */
        .theme-icon {
            transition: transform 0.5s ease;
        }
        
        .theme-switch:hover .theme-icon {
            transform: rotate(180deg);
        }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('projects.index') }}">
                <i class="fas fa-robot me-2"></i>IA Generator
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('projects.index') }}">
                            <i class="fas fa-home me-1"></i> Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-coins me-1"></i> Créditos: <span class="badge bg-light text-dark">100</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link theme-switch" href="#" id="themeSwitch">
                            <i class="fas fa-sun theme-icon" id="themeIcon"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i> Sair
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus"></i> Registro
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link theme-switch" href="#" id="themeSwitch">
                            <i class="fas fa-sun theme-icon" id="themeIcon"></i>
                        </a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
            @if(!request()->routeIs('login') && !request()->routeIs('register'))
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('projects.index') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                                <i class="fas fa-project-diagram"></i> Meus Projetos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('projects.create') ? 'active' : '' }}" href="{{ route('projects.create') }}">
                                <i class="fas fa-plus-circle"></i> Novo Projeto
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('wordpress.*') ? 'active' : '' }}" href="{{ route('wordpress.index') }}">
                                <i class="fab fa-wordpress"></i> Sites WordPress
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-shopping-cart"></i> Comprar Créditos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-cog"></i> Configurações
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-question-circle"></i> Ajuda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('connections.*') ? 'active' : '' }}" href="{{ route('connections.index') }}">
                                <i class="fas fa-plug"></i> Conexões
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            @else
            <main class="col-12 px-md-4 py-4">
            @endif
            @else
            <main class="col-12 px-md-4 py-4">
            @endauth
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Tema claro/escuro
        document.addEventListener('DOMContentLoaded', function() {
            const themeSwitch = document.getElementById('themeSwitch');
            const themeIcon = document.getElementById('themeIcon');
            const htmlElement = document.documentElement;
            
            // Já aplicamos o tema no início da página, agora só atualizamos o ícone
            updateThemeIcon(htmlElement.getAttribute('data-bs-theme'));
            
            themeSwitch.addEventListener('click', function(e) {
                e.preventDefault();
                
                const currentTheme = htmlElement.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                htmlElement.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                updateThemeIcon(newTheme);
            });
            
            function updateThemeIcon(theme) {
                if (theme === 'dark') {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                } else {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                }
            }
        });
    </script>
    @yield('scripts')
</body>
</html> 