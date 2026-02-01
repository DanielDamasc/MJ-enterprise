<?php

namespace Database\Factories;

use App\Models\Client;
use Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Client::class;

    public function definition(): array
    {
        $tipo = Arr::random(['residencial', 'comercial']);
        $isComercial = $tipo === 'comercial';

        return [
            'cliente' => $isComercial ? fake()->company() : fake()->name(),
            'contato' => fake()->firstName(),
            'telefone' => $this->gerarTelefoneCelular(),
            'email' => fake()->unique()->safeEmail(),
            'tipo' => $tipo,

            // 70% de chance de ser null e 30% de ter uma data.
            'ultima_notificacao' => fake()->optional(0.3)->dateTimeBetween('-1 year', 'now'),
            'qtd_notificacoes' => fake()->numberBetween(0,2)
        ];
    }

    private function gerarTelefoneCelular(): string
    {
        $ddd = random_int(11, 99);

        $numero = '9' . random_int(10000000, 99999999);

        return (string) $ddd . $numero;
    }

    // Cria um cliente que nunca foi notificado.
    public function nuncaNotificado()
    {
        return $this->state(fn (array $attributes) => [
            'ultima_notificacao' => null,
            'qtd_notificacoes' => 0,
        ]);
    }

    // Cria um cliente que foi notificado ontem para verificar spam.
    public function recemNotificado()
    {
        return $this->state(fn (array $attributes) => [
            'ultima_notificacao' => now()->subDay(),
            'qtd_notificacoes' => 1,
        ]);
    }
}
