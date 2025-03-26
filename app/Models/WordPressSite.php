<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WordPressSite extends Model
{
    use HasFactory;
    
    // Especificar o nome correto da tabela
    protected $table = 'wordpress_sites';
    
    protected $fillable = [
        'name',
        'url',
        'username',
        'api_token',
        'user_id',
        'settings'
    ];
    
    protected $casts = [
        'settings' => 'array'
    ];
    
    /**
     * Obtém o usuário dono do site WordPress
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Obtém os projetos associados a este site WordPress
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
} 