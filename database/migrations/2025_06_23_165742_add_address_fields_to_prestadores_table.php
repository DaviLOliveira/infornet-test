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
        Schema::table('prestadores', function (Blueprint $table) {
            $table->string('logradouro')->nullable()->after('nome');
            $table->string('numero')->nullable()->after('logradouro'); // Pode ser string para aptos/blocos
            $table->string('bairro')->nullable()->after('numero');
            $table->string('cidade')->nullable()->after('bairro');
            $table->string('UF', 2)->nullable()->after('cidade'); // UF com 2 caracteres
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prestadores', function (Blueprint $table) {
            
            $table->dropColumn('UF');
            $table->dropColumn('cidade');
            $table->dropColumn('bairro');
            $table->dropColumn('numero');
            $table->dropColumn('logradouro');
        });
    }
};