<!DOCTYPE html>
<html lang="pt-BR">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IA Generator - Gerador de Conteúdo com IA para WordPress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4348d7;
            --secondary-color: #b629ff;
            --light-bg: #f8f9fa;
            --dark-text: #333;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            color: var(--dark-text);
            min-height: 100vh;
        }
        
        .navbar {
            background: linear-gradient(76.35deg, var(--primary-color) 40.09%, var(--secondary-color) 125.4%);
        }
        
        .hero {
            background: linear-gradient(76.35deg, var(--primary-color) 40.09%, var(--secondary-color) 125.4%);
            color: white;
            padding: 100px 0;
            border-radius: 0 0 50px 50px;
            margin-bottom: 50px;
        }
        
        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .hero p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .feature-card {
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: var(--primary-color);
        }
        
        .btn-primary {
            background: linear-gradient(76.35deg, var(--primary-color) 40.09%, var(--secondary-color) 125.4%);
            border: none;
            padding: 10px 25px;
            border-radius: 30px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(76.35deg, var(--primary-color) 50.09%, var(--secondary-color) 135.4%);
            box-shadow: 0 5px 15px rgba(67, 72, 215, 0.3);
        }
        
        .btn-outline-light {
            border-radius: 30px;
            padding: 10px 25px;
        }
        
        footer {
            background: linear-gradient(76.35deg, var(--primary-color) 40.09%, var(--secondary-color) 125.4%);
            color: white;
            padding: 50px 0 20px;
            margin-top: 100px;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: white;
        }
            </style>
    </head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fas fa-robot me-2"></i>
                <span>IA Generator</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
            @if (Route::has('login'))
                    @auth
                            <li class="nav-item">
                                <a href="{{ url('/dashboard') }}" class="nav-link">Dashboard</a>
                            </li>
                    @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="nav-link">Login</a>
                            </li>
                        @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="nav-link">Registrar</a>
                                </li>
                        @endif
                    @endauth
                    @endif
                </ul>
            </div>
        </div>
                </nav>

    <section class="hero">
        <div class="container text-center">
            <h1>Crie Conteúdo Incrível com IA</h1>
            <p class="mb-5">Gere posts para WordPress automaticamente usando inteligência artificial avançada</p>
            <div class="d-flex justify-content-center gap-3">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg">Começar Agora</a>
                @endif
                <a href="#features" class="btn btn-outline-light btn-lg">Saiba Mais</a>
            </div>
        </div>
    </section>

    <section class="container mb-5" id="features">
        <h2 class="text-center mb-5">Recursos Poderosos</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-magic"></i>
                        </div>
                        <h4>Geração de Conteúdo com IA</h4>
                        <p>Crie posts de alta qualidade em segundos com nossa tecnologia de IA avançada.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon">
                            <i class="fab fa-wordpress"></i>
                        </div>
                        <h4>Integração com WordPress</h4>
                        <p>Publique diretamente no seu site WordPress com apenas um clique.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <h4>Gerenciamento de Projetos</h4>
                        <p>Organize seu conteúdo em projetos para manter tudo organizado.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container mb-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2>Como Funciona</h2>
                <p class="lead">Gerar conteúdo nunca foi tão fácil</p>
                <div class="d-flex mb-4">
                    <div class="me-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">1</div>
                    </div>
                    <div>
                        <h5>Conecte seu WordPress</h5>
                        <p>Conecte seu site WordPress usando nosso plugin simples.</p>
                    </div>
                </div>
                <div class="d-flex mb-4">
                    <div class="me-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">2</div>
                    </div>
                    <div>
                        <h5>Crie um Projeto</h5>
                        <p>Configure um novo projeto para organizar seu conteúdo.</p>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="me-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">3</div>
                    </div>
                    <div>
                        <h5>Gere Conteúdo</h5>
                        <p>Escolha um tipo de post, forneça algumas instruções e deixe a IA fazer o resto.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <img src="https://via.placeholder.com/600x400?text=IA+Generator" alt="Como funciona" class="img-fluid rounded shadow">
            </div>
        </div>
    </section>

    <section class="container text-center mb-5">
        <h2 class="mb-4">Pronto para Revolucionar sua Criação de Conteúdo?</h2>
        <p class="lead mb-4">Junte-se a milhares de criadores de conteúdo que economizam tempo com o IA Generator</p>
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Começar Gratuitamente</a>
            @endif
    </section>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">IA Generator</h5>
                    <p class="text-white-50">Transformando a criação de conteúdo com inteligência artificial avançada.</p>
                </div>
                <div class="col-md-2 mb-4 mb-md-0">
                    <h6>Produto</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Recursos</a></li>
                        <li><a href="#">Preços</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4 mb-md-0">
                    <h6>Empresa</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Sobre nós</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Contato</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4 mb-md-0">
                    <h6>Legal</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Termos</a></li>
                        <li><a href="#">Privacidade</a></li>
                        <li><a href="#">Cookies</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6>Redes Sociais</h6>
                    <div class="d-flex">
                        <a href="#" class="me-3 text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="me-3 text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="me-3 text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <hr class="mt-4 mb-3 bg-white-50">
            <div class="text-center text-white-50">
                <small>&copy; 2023 IA Generator. Todos os direitos reservados.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
