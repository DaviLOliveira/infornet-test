<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestador extends Model
{
    use HasFactory;

    protected $table = 'prestadores'; 

    public function servicosPrestados()
    {
        // Define a relação Muitos-para-Muitos com o modelo Servico,
        // usando a tabela 'servico_prestador' como pivot.
        return $this->belongsToMany(Servico::class, 'servico_prestador', 'prestador_id', 'servico_id')
                    ->withPivot('km_de_saida', 'valor_de_saida', 'valor_por_km_excedente');
    }
}