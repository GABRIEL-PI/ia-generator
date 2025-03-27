<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedTitle extends Model
{
    use HasFactory;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'keywords',
        'support_keywords',
        'style',
        'user_id',
        'used',
        'project_id'
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'used' => 'boolean',
    ];

    /**
     * Obtém o usuário que criou este título.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtém o projeto associado a este título, se houver.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
} 