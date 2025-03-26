<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determina se o usuário pode visualizar o projeto
     */
    public function view(User $user, Project $project)
    {
        return $user->id === $project->user_id;
    }
    
    /**
     * Determina se o usuário pode atualizar o projeto
     */
    public function update(User $user, Project $project)
    {
        return $user->id === $project->user_id;
    }
    
    /**
     * Determina se o usuário pode excluir o projeto
     */
    public function delete(User $user, Project $project)
    {
        return $user->id === $project->user_id;
    }
} 