<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Realiza o login, retornando um token JWT e o nome do usuário.
     */
    public function login(Request $request)
    {
        // Validação dos dados de entrada
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Tenta autenticar e gerar o token usando o guard 'api'
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Se o login for bem-sucedido, retorna o token e o nome do usuário 
        return response()->json([
            'token' => $token,
            'user_name' => auth('api')->user()->name,
        ]);
    }

    /**
     * Realiza o logout do usuário (invalida o token).
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}