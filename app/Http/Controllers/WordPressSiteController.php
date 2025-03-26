<?php

namespace App\Http\Controllers;

use App\Models\WordPressSite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WordPressSiteController extends Controller
{
    /**
     * Exibe a lista de sites WordPress conectados
     */
    public function index()
    {
        $sites = WordPressSite::where('user_id', auth()->id())->get();
        
        return view('wordpress.index', compact('sites'));
    }
    
    /**
     * Exibe o formulário para conectar um novo site WordPress
     */
    public function create()
    {
        return view('wordpress.create');
    }
    
    /**
     * Armazena um novo site WordPress
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'username' => 'required|string|max:255',
        ]);
        
        // Gerar um token de API seguro
        $apiToken = Str::random(64);
        
        $site = WordPressSite::create([
            'name' => $request->name,
            'url' => rtrim($request->url, '/'),
            'username' => $request->username,
            'api_token' => $apiToken,
            'user_id' => auth()->id(),
            'settings' => []
        ]);
        
        return redirect()->route('wordpress.index')
            ->with('success', 'Site WordPress conectado com sucesso!')
            ->with('generated_token', $apiToken)
            ->with('site_id', $site->id);
    }
    
    /**
     * Remove um site WordPress
     */
    public function destroy(WordPressSite $wordPressSite)
    {
        $this->authorize('delete', $wordPressSite);
        
        $wordPressSite->delete();
        
        return redirect()->route('wordpress.index')
            ->with('success', 'Site WordPress desconectado com sucesso!');
    }
    
    /**
     * Testa a conexão com um site WordPress
     */
    public function testConnection(WordPressSite $wordPressSite)
    {
        $this->authorize('view', $wordPressSite);
        
        try {
            $response = Http::withBasicAuth(
                $wordPressSite->username, 
                $wordPressSite->api_token
            )->get($wordPressSite->url . '/wp-json/api-connector/v1/posts?per_page=1');
            
            if ($response->successful()) {
                return response()->json(['status' => 'success', 'message' => 'Conexão bem-sucedida!']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Falha na conexão: ' . $response->body()], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro de conexão: ' . $e->getMessage()], 500);
        }
    }
} 