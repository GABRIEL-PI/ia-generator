<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'description',
        'wordpress_site_id',
        'user_id',
        'settings'
    ];
    
    protected $casts = [
        'settings' => 'array'
    ];
    
    /**
     * Obtém o usuário dono do projeto
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Obtém o site WordPress associado ao projeto
     */
    public function wordPressSite()
    {
        return $this->belongsTo(WordPressSite::class, 'wordpress_site_id');
    }
    
    /**
     * Obtém os posts gerados para este projeto
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
} 