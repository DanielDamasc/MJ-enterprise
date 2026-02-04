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
        Schema::create('air_conditioners', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')->constrained('clients')->onDelete('cascade');

            $table->date('prox_higienizacao')->nullable();

            $table->string('codigo_ac');
            $table->string('ambiente')->nullable();
            $table->string('modelo')->nullable();
            $table->string('marca')->nullable();
            $table->integer('potencia');
            $table->string('tipo');
            $table->string('tipo_gas')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('air_conditioners');
    }
};
