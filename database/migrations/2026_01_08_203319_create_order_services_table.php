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
        Schema::create('order_services', function (Blueprint $table) {
            $table->id();

            // onDelete restrict para prevenir deletar um ar-condicionado que tenha service.
            $table->foreignId('cliente_id')->constrained('clients')->onDelete('restrict');
            $table->foreignId('executor_id')->constrained('users');

            $table->string('tipo');
            $table->date('data_servico');
            $table->time('horario')->nullable();
            $table->decimal('total', 10, 2);
            $table->string('status')->default('agendado');

            $table->text('observacoes_executor')->nullable();

            $table->json('detalhes')->nullable();

            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_services');
    }
};
