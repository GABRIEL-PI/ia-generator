<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'user_id')) {
                $table->foreignId('user_id')->after('project_id')->constrained()->onDelete('cascade');
            }
            
            // Verificar e adicionar outras colunas que possam estar faltando
            if (!Schema::hasColumn('posts', 'settings')) {
                $table->json('settings')->nullable()->after('wordpress_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            
            if (Schema::hasColumn('posts', 'settings')) {
                $table->dropColumn('settings');
            }
        });
    }
}; 