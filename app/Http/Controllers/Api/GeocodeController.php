<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class GeocodeController extends Controller
{
    /**
     * Busca as coordenadas de um endereço usando uma API externa.
     */
    public function buscar(Request $request)
    {
        // Validação da entrada
        $validator = Validator::make($request->all(), [
            'endereco' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $endereco = $request->input('endereco');
        
        // Credenciais da API externa - **IMPORTANTE:** Mova isso para o seu arquivo .env!
        $apiUrl = 'https://nhen90f0j3.execute-api.us-east-1.amazonaws.com/v1/api/endereco/geocode/'; // 
        $apiUser = env('GEOCODE_API_USER', 'teste-Infornet'); // 
        $apiPassword = env('GEOCODE_API_PASSWORD', 'c@nsulta-dados-ap1-teste-Infornet#24'); // 

        // Realiza a chamada HTTP com Basic Auth 
        $response = Http::withBasicAuth($apiUser, $apiPassword)
                        ->get($apiUrl . urlencode($endereco));

        // Verifica se a requisição foi bem-sucedida
        if ($response->successful()) {
            // Retorna a resposta da API externa 
            return response()->json($response->json());
        }

        // Retorna um erro caso a API externa falhe
        return response()->json(['error' => 'Falha ao buscar coordenadas'], $response->status());
    }
}