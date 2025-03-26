<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'project_id',
        'user_id',
        'status',
        'wordpress_id',
        'wordpress_url',
        'settings',
    ];
    
    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'settings' => 'array',
    ];
    
    /**
     * Obtém o projeto ao qual este post pertence
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    /**
     * Obtém o usuário que criou este post
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 