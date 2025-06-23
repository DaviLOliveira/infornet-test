<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prestador;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // Importe a fachada Log

class PrestadorController extends Controller
{
    /**
     * Função principal de busca de prestadores, conforme os requisitos.
     */
    public function buscar(Request $request)
    {
        // 1. Validação da Entrada
        $validator = Validator::make($request->all(), [
            'latitude_origem' => 'required|numeric',
            'longitude_origem' => 'required|numeric',
            'latitude_destino' => 'required|numeric',
            'longitude_destino' => 'required|numeric',
            'servico_id' => 'required|integer|exists:servicos,id',
            'quantidade' => 'sometimes|integer|min:1|max:100',
            'ordenacao' => 'sometimes|array',
            'filtros' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Construção da Query Base
        $query = Prestador::query()->where('situacao', true);

        // Filtra prestadores que oferecem o serviço solicitado
        // Carrega a relação 'servicosPrestados' e filtra pelo servico_id na tabela pivô
        // Com `whereHas` e `with` no mesmo serviço, garantimos que apenas os prestadores
        // que oferecem o serviço são selecionados e que os dados do pivô são carregados.
        $query->whereHas('servicosPrestados', function ($q) use ($request) {
            $q->where('servico_id', $request->servico_id);
        })->with(['servicosPrestados' => function ($query) use ($request) {
            $query->where('servicos.id', $request->servico_id)
                  ->withPivot('km_de_saida', 'valor_de_saida', 'valor_por_km_excedente');
        }]);

        // Aplica filtros de cidade e estado, se informados
        if ($request->has('filtros.cidade')) {
            $query->where('cidade', $request->input('filtros.cidade'));
        }
        if ($request->has('filtros.estado')) {
            $query->where('UF', $request->input('filtros.estado'));
        }

        // Pega os prestadores com a relação eager loaded
        $prestadores = $query->get();
        
        // 3. Processamento dos Prestadores (Cálculos e API externa)
        $resultado = $prestadores->map(function ($prestador) use ($request) {
            // Acessa o primeiro serviço na coleção carregada. Como filtramos com 'whereHas' e 'with',
            // esperamos que haja apenas um serviço correspondente na coleção 'servicosPrestados'.
            $servicoInfo = $prestador->servicosPrestados->first();

            // Inicializa valores padrão caso o serviço ou seus dados do pivô não sejam encontrados
            $distanciaTotal = 0;
            $valorTotal = 0;
            $statusOnline = 'offline'; // Valor padrão para status online

            if ($servicoInfo && $servicoInfo->pivot) { // Verifica se o serviço e os dados do pivô existem
                // Cálculo de distância total
                // A ordem dos pontos é: Prestador -> Origem -> Destino -> Prestador
                $distancia1 = $this->calcularDistancia(
                    $prestador->latitude, $prestador->longitude,
                    $request->latitude_origem, $request->longitude_origem
                );
                $distancia2 = $this->calcularDistancia(
                    $request->latitude_origem, $request->longitude_origem,
                    $request->latitude_destino, $request->longitude_destino
                );
                $distancia3 = $this->calcularDistancia(
                    $request->latitude_destino, $request->longitude_destino,
                    $prestador->latitude, $prestador->longitude
                );
                $distanciaTotal = $distancia1 + $distancia2 + $distancia3;
                
                // Calcula o valor do serviço
                $valorTotal = $servicoInfo->pivot->valor_de_saida; // Acessa os dados do pivô
                if ($distanciaTotal > $servicoInfo->pivot->km_de_saida) { // Acessa os dados do pivô
                    $kmExcedente = $distanciaTotal - $servicoInfo->pivot->km_de_saida;
                    $valorTotal += $kmExcedente * $servicoInfo->pivot->valor_por_km_excedente; // Acessa os dados do pivô
                }
            }

            // Adiciona os dados calculados ao objeto do prestador
            $prestador->distancia_total = round($distanciaTotal, 2);
            $prestador->valor_total_servico = round($valorTotal, 2);
            // O status online será atualizado em um passo separado abaixo

            return $prestador;
        });

        // 4. Busca de Status Online (executada após todos os cálculos de distância/valor)
        if ($resultado->isNotEmpty()) {
            $apiUser = env('GEOCODE_API_USER', 'teste-Infornet');
            $apiPassword = env('GEOCODE_API_PASSWORD', 'c@nsulta-dados-ap1-teste-Infornet#24');
            
            // Cria um array de IDs dos prestadores para a requisição da API de status
            $prestadorIds = $resultado->pluck('id')->all();

            try {
                $statusResponse = Http::withBasicAuth($apiUser, $apiPassword)
                    ->post('https://nhen90f0j3.execute-api.us-east-1.amazonaws.com/v1/api/prestadores/online', [
                        'prestadores' => $prestadorIds
                    ]);

                if ($statusResponse->successful()) {
                    $statusData = collect($statusResponse->json()['data']);
                    // Mapeia o status para cada prestador
                    $resultado = $resultado->map(function ($prestador) use ($statusData) {
                        $statusInfo = $statusData->firstWhere('id', $prestador->id);
                        $prestador->status_online = $statusInfo ? $statusInfo['status'] : 'offline';
                        return $prestador;
                    });
                } else {
                    // Log do erro da API de status, caso a chamada não seja bem-sucedida
                    Log::error('Erro ao buscar status online dos prestadores: ' . $statusResponse->body());
                    // Define status offline para todos em caso de falha na API
                    $resultado = $resultado->map(function ($prestador) {
                        $prestador->status_online = 'offline';
                        return $prestador;
                    });
                }
            } catch (\Exception $e) {
                // Captura exceções de rede ou outras
                Log::error('Exceção ao chamar API de status online: ' . $e->getMessage());
                // Define status offline para todos em caso de exceção
                $resultado = $resultado->map(function ($prestador) {
                    $prestador->status_online = 'offline';
                    return $prestador;
                });
            }
        }
        
        // 5. Ordenação
        if ($request->has('ordenacao')) {
            foreach ($request->input('ordenacao') as $campo => $direcao) {
                $direcaoDesc = strtolower($direcao) === 'desc';
                if ($campo === 'valor_total') {
                    $resultado = $resultado->sortBy('valor_total_servico', SORT_REGULAR, $direcaoDesc);
                } elseif ($campo === 'distancia_total') {
                    $resultado = $resultado->sortBy('distancia_total', SORT_REGULAR, $direcaoDesc);
                }
            }
        }

        // 6. Limita a quantidade
        $quantidade = $request->input('quantidade', 10);
        $resultado = $resultado->take($quantidade);

        return response()->json($resultado->values());
    }
    
    /**
     * Calcula a distância em KM entre dois pontos de latitude/longitude usando a fórmula de Haversine.
     * Mais precisa para distâncias no globo.
     */
    private function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Raio médio da Terra em quilômetros

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c; // Distância em km

        return $distance;
    }
}
