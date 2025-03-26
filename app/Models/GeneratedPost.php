<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedPost extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'content',
        'wordpress_post_id',
        'project_id',
        'user_id',
        'status',
        'settings',
        'credits_used'
    ];
    
    protected $casts = [
        'settings' => 'array'
    ];
    
    /**
     * Obtém o usuário que gerou o post
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Obtém o projeto ao qual o post pertence
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
} 