<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fazer backup dos dados existentes
        $posts = [];
        if (Schema::hasTable('posts')) {
            $posts = DB::table('posts')->get()->toArray();
            
            // Remover a tabela existente
            Schema::dropIfExists('posts');
        }
        
        // Criar a tabela com a estrutura correta
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('draft'); // draft, published
            $table->unsignedBigInteger('wordpress_id')->nullable();
            $table->string('wordpress_url')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
        
        // Restaurar os dados com user_id válido
        foreach ($posts as $post) {
            $postData = (array) $post;
            
            // Se não tiver user_id, obter do projeto
            if (!isset($postData['user_id']) || is_null($postData['user_id'])) {
                $project = DB::table('projects')->find($postData['project_id']);
                if ($project) {
                    $postData['user_id'] = $project->user_id;
                } else {
                    // Se não encontrar o projeto, usar o ID do primeiro usuário
                    $firstUser = DB::table('users')->first();
                    if ($firstUser) {
                        $postData['user_id'] = $firstUser->id;
                    } else {
                        // Pular este post se não conseguir encontrar um user_id válido
                        continue;
                    }
                }
            }
            
            // Remover o ID para que seja gerado automaticamente
            unset($postData['id']);
            
            // Inserir o post
            DB::table('posts')->insert($postData);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
}; 