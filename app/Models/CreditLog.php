<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'action',
        'amount',
        'description',
        'created_at'
    ];
    
    public $timestamps = false;
    
    /**
     * Obtém o usuário associado a este log
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 