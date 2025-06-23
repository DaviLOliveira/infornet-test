<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servico; 
class ServicoSeeder extends Seeder
{
    public function run(): void
    {
        Servico::create(['nome' => 'Reboque', 'situacao' => true]);
        Servico::create(['nome' => 'Chaveiro', 'situacao' => true]);
        Servico::create(['nome' => 'Troca de Pneu', 'situacao' => true]);
        Servico::create(['nome' => 'Pane Seca', 'situacao' => false]); // Exemplo de um serviço inativo
    }
}