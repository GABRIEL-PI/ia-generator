<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'webhook_url',
        'api_key',
        'user_id',
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
     * Obtém o usuário que possui esta conexão.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 