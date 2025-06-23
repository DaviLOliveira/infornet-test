<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;

   
    protected $table = 'servicos';

    protected $fillable = [
        'nome',
        'situacao',
    ];

    public function prestadores()
    {
        return $this->belongsToMany(Prestador::class, 'servico_prestador',
            'servico_id', 'prestador_id')
            ->withPivot('km_de_saida', 'valor_de_saida', 'valor_por_km_excedente');
    }
}
