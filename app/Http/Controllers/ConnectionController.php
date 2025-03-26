<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Connection;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class ConnectionController extends Controller
{
    /**
     * Exibe a lista de conexões do usuário
     */
    public function index()
    {
        $connections = Connection::where('user_id', Auth::id())->get();
        return view('connections.index', compact('connections'));
    }

    /**
     * Exibe o formulário para criar uma nova conexão
     */
    public function create()
    {
        return view('connections.create');
    }

    /**
     * Armazena uma nova conexão
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:make,n8n,typebot,zapier',
            'webhook_url' => 'required|url',
            'api_key' => 'nullable|string',
            'settings' => 'nullable|array'
        ]);
        
        $connection = Connection::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'webhook_url' => $validated['webhook_url'],
            'api_key' => $validated['api_key'] ?? null,
            'user_id' => Auth::id(),
            'settings' => $validated['settings'] ?? []
        ]);
        
        return redirect()->route('connections.index')
            ->with('success', 'Conexão criada com sucesso!');
    }

    /**
     * Exibe uma conexão específica
     */
    public function show(Connection $connection)
    {
        // Verificar se o usuário tem permissão para ver esta conexão
        if ($connection->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para acessar esta conexão.');
        }
        
        return view('connections.show', compact('connection'));
    }

    /**
     * Exibe o formulário para editar uma conexão
     */
    public function edit(Connection $connection)
    {
        // Verificar se o usuário tem permissão para editar esta conexão
        if ($connection->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para editar esta conexão.');
        }
        
        return view('connections.edit', compact('connection'));
    }

    /**
     * Atualiza uma conexão
     */
    public function update(Request $request, Connection $connection)
    {
        // Verificar se o usuário tem permissão para atualizar esta conexão
        if ($connection->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para atualizar esta conexão.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:make,n8n,typebot,zapier',
            'webhook_url' => 'required|url',
            'api_key' => 'nullable|string',
            'settings' => 'nullable|array'
        ]);
        
        $connection->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'webhook_url' => $validated['webhook_url'],
            'api_key' => $validated['api_key'] ?? null,
            'settings' => $validated['settings'] ?? []
        ]);
        
        return redirect()->route('connections.index')
            ->with('success', 'Conexão atualizada com sucesso!');
    }

    /**
     * Remove uma conexão
     */
    public function destroy(Connection $connection)
    {
        // Verificar se o usuário tem permissão para remover esta conexão
        if ($connection->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para remover esta conexão.');
        }
        
        $connection->delete();
        
        return redirect()->route('connections.index')
            ->with('success', 'Conexão removida com sucesso!');
    }

    /**
     * Publica um post via Make.com
     */
    public function publishPost(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        
        // Verificar se o usuário tem permissão para publicar este post
        if ($post->project->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para publicar este post.');
        }
        
        $validated = $request->validate([
            'connection_id' => 'required|exists:connections,id',
            'status' => 'required|in:draft,publish'
        ]);
        
        // Verificar se a conexão pertence ao usuário
        $connection = Connection::findOrFail($validated['connection_id']);
        if ($connection->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para usar esta conexão.');
        }
        
        // Aqui você implementaria a lógica de envio para o Make.com
        // Por enquanto, apenas simulamos o sucesso
        
        return redirect()->back()->with('success', 'Post enviado para Make.com com sucesso!');
    }
} 