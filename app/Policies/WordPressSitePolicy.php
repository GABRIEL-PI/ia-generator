<?php

namespace App\Policies;

use App\Models\WordPressSite;
use App\Models\User;

class WordPressSitePolicy
{
    /**
     * Determina se o usuário pode visualizar o site WordPress
     */
    public function view(User $user, WordPressSite $wordPressSite)
    {
        return $user->id === $wordPressSite->user_id;
    }
    
    /**
     * Determina se o usuário pode atualizar o site WordPress
     */
    public function update(User $user, WordPressSite $wordPressSite)
    {
        return $user->id === $wordPressSite->user_id;
    }
    
    /**
     * Determina se o usuário pode excluir o site WordPress
     */
    public function delete(User $user, WordPressSite $wordPressSite)
    {
        return $user->id === $wordPressSite->user_id;
    }
} 