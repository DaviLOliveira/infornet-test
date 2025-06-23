<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Prestador; 
use App\Models\Servico;   
class PrestadorSeeder extends Seeder
{
    
    public function run(): void
    {
        

        // Usa o caminho completo para a classe Servico
        $servicosDisponiveis = \App\Models\Servico::all(); // Pega todos os serviços existentes

        // Adicionar um loop para criar múltiplos prestadores rapidamente
        for ($i = 1; $i <= 10; $i++) { 
            $prestador = Prestador::create([
                'nome' => "Prestador Teste {$i}",
                'email' => "prestador{$i}@example.com",
                'telefone' => "3198765-100{$i}",
                'latitude' => -19.9190 + (mt_rand(-100, 100) / 10000), // Varia um pouco a lat/lng
                'longitude' => -43.9386 + (mt_rand(-100, 100) / 10000),
                'situacao' => (bool) (mt_rand(0, 1)), // Aleatoriamente online/offline
                'logradouro' => "Rua Exemplo {$i}",
                'numero' => (string) (100 + $i),
                'bairro' => 'Bairro Teste',
                'cidade' => ($i % 2 == 0) ? 'Belo Horizonte' : 'Contagem', // Alterna cidades
                'UF' => 'MG',
            ]);

            
            $servicosParaAnexar = $servicosDisponiveis->random(min(3, $servicosDisponiveis->count()));

            foreach ($servicosParaAnexar as $servico) {
                
                if (!$prestador->servicosPrestados->contains($servico->id)) {
                    $prestador->servicosPrestados()->attach($servico->id, [
                        'km_de_saida' => mt_rand(5, 20) / 1.0,
                        'valor_de_saida' => mt_rand(30, 80) / 1.0,
                        'valor_por_km_excedente' => mt_rand(10, 50) / 10.0,
                    ]);
                }
            }
        }
    }
}