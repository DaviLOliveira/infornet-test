<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servico_prestador', function (Blueprint $table) {
            // Chaves estrangeiras para as tabelas envolvidas
            $table->foreignId('servico_id')->constrained('servicos')->onDelete('cascade');
            $table->foreignId('prestador_id')->constrained('prestadores')->onDelete('cascade');

            // Colunas adicionais para a tabela pivô
            $table->double('km_de_saida', 8, 2); 
            $table->double('valor_de_saida', 8, 2);
            $table->double('valor_por_km_excedente', 8, 2);

            // Define a chave primária composta (evita duplicatas e acelera consultas)
            $table->primary(['servico_id', 'prestador_id']);

            $table->timestamps(); // created_at e updated_at
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('servico_prestador');
    }
};