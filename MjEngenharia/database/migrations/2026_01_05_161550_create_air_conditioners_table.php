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

            $table->string('codigo_ac');

            $table->string('ambiente')->nullable();

            $table->date('instalacao');
            $table->date('prox_higienizacao');

            $table->string('marca');
            $table->integer('potencia');
            $table->string('tipo');

            $table->decimal('valor');
            $table->boolean('valor_com_material');

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
