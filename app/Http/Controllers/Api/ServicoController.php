<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Servico;
use Illuminate\Http\Request;

class ServicoController extends Controller
{
    /**
     * Retorna uma listagem dos serviços ativos.
     */
    public function index()
    {
        // Busca todos os serviços onde a situação é 'true' (ativo) 
        $servicos = Servico::where('situacao', true)->get();

        return response()->json($servicos);
    }
}