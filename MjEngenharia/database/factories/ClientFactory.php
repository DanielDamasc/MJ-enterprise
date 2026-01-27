<?php

namespace Database\Factories;

use Arr;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

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
    public function definition(): array
    {
        return [
            'cliente' => fake()->name(),
            'contato' => fake()->name(),
            'telefone' => $this->gerarTelefoneCelular(),
            'email' => fake()->unique()->safeEmail(),
            'tipo' => Arr::random(['residencial', 'comercial']),
            'ultima_notificacao' => null,
            'qtd_notificacoes' => 0
        ];
    }

    private function gerarTelefoneCelular(): string
    {
        $ddd = random_int(11, 99);

        $numero = '9' . random_int(10000000, 99999999);

        return (string) $ddd . $numero;
    }
}
