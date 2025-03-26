<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\WordPressSiteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rotas de autenticação
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Rota de logout (acessível apenas para usuários autenticados)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Rotas para projetos
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return redirect()->route('projects.index');
    })->name('dashboard');
    
    // Projetos
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    
    // Rotas para posts (formato novo)
    Route::get('/projects/{project}/posts/create', [ProjectController::class, 'createPost'])->name('projects.create-post');
    Route::post('/projects/{project}/posts', [ProjectController::class, 'generatePost'])->name('projects.generate-post');
    
    // Rotas para posts (formato antigo para compatibilidade)
    Route::get('/projects/{project}/create-post', [ProjectController::class, 'createPost']);
    Route::post('/projects/{project}/generate-post', [ProjectController::class, 'generatePost']);
    
    // Sites WordPress
    Route::get('/wordpress', [WordPressSiteController::class, 'index'])->name('wordpress.index');
    Route::get('/wordpress/create', [WordPressSiteController::class, 'create'])->name('wordpress.create');
    Route::post('/wordpress', [WordPressSiteController::class, 'store'])->name('wordpress.store');
    Route::delete('/wordpress/{wordPressSite}', [WordPressSiteController::class, 'destroy'])->name('wordpress.destroy');
    Route::post('/wordpress/{wordPressSite}/test', [WordPressSiteController::class, 'testConnection'])->name('wordpress.test');
    
    // Rotas para geração em massa
    Route::get('/bulk-generate', [ProjectController::class, 'bulkGenerate'])
        ->name('projects.bulk-generate');
    
    Route::post('/bulk-generate', [ProjectController::class, 'storeBulkGenerate'])
        ->name('projects.bulk-generate.store');
    
    // Rota para gerar títulos via API
    Route::post('/api/generate-titles', [ProjectController::class, 'generateTitles'])
        ->name('api.generate-titles');
});

// Rotas para posts
Route::middleware(['auth'])->group(function () {
    // Certifique-se de que esta rota existe e está correta
    Route::post('/projects/{project}/posts', [ProjectController::class, 'generatePost'])->name('projects.generate-post');
    
    // Rota para visualização do post
    Route::get('/posts/{postId}/preview', [ProjectController::class, 'previewPost'])->name('posts.preview');
    
    // Rota para publicação do post
    Route::post('/posts/{postId}/publish', [ProjectController::class, 'publishPost'])->name('posts.publish');
});

// Rota para baixar conteúdo de URL
Route::post('/download-url', [App\Http\Controllers\ProjectController::class, 'downloadUrl'])->name('download.url');

// Rotas para conexões
Route::resource('connections', App\Http\Controllers\ConnectionController::class);

// Rota para atualizar o conteúdo do post
Route::post('/posts/{postId}/update', [App\Http\Controllers\ProjectController::class, 'updatePost'])->name('posts.update');

// Rota para agendamento de posts
Route::post('/posts/schedule', [App\Http\Controllers\ProjectController::class, 'schedulePost'])->name('posts.schedule');

// Rota para publicação
Route::post('/connections/publish/{postId}', [App\Http\Controllers\ConnectionController::class, 'publishPost'])->name('connections.publish');

// Rota para resetar agendamento
Route::post('/posts/reset-schedule', [App\Http\Controllers\ProjectController::class, 'resetSchedule'])->name('posts.reset-schedule');

// Rota para geração de imagens
Route::post('/posts/{postId}/generate-images', [App\Http\Controllers\ProjectController::class, 'generateImages'])->name('posts.generate-images');



// Rotas de autenticação
//require __DIR__.'/auth.php';
