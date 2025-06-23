<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServicoController;
use App\Http\Controllers\Api\GeocodeController;
use App\Http\Controllers\Api\PrestadorController;

/*
|--------------------------------------------------------------------------
| Rotas da API
|-------------------------------------------------------------------------
*/

// --- Endpoint de Autenticação (Público) ---
// Recebe login/senha e retorna o token JWT e o nome do usuário.
Route::post('/login', [AuthController::class, 'login'])->name('login');


// --- Endpoints Protegidos por JWT ---
// Todas as rotas dentro deste grupo exigirão um token JWT válido para serem acessadas.
Route::group(['middleware' => ['auth:api']], function () {

    // Endpoint para buscar os serviços disponíveis
    // Requisito: "Busca de serviços disponíveis"
    Route::get('/servicos', [ServicoController::class, 'index']);

    // Endpoint para buscar coordenadas de um endereço
    // Requisito: "Buscar coordenadas"
    Route::get('/coordenadas', [GeocodeController::class, 'buscar']);

    // Endpoint principal para a busca de prestadores
    // Requisito: "Buscar prestadores"
    Route::post('/prestadores/buscar', [PrestadorController::class, 'buscar']);

    // Endpoint de Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});