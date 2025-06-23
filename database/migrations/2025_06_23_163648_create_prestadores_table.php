<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prestadores', function (Blueprint $table) {
            $table->id(); // ID primário auto-incremento
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('telefone')->nullable();
            $table->double('latitude')->nullable(); // Para a localização inicial do prestador, se houver
            $table->double('longitude')->nullable(); // Para a localização inicial do prestador, se houver
            $table->boolean('situacao')->default(true); // Para indicar se o prestador está ativo
            $table->timestamps(); // created_at e updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestadores');
    }
};