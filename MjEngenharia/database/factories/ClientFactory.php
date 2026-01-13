<?php

namespace Database\Factories;

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
    public function definition(): array
    {
        return [
            'cliente' => fake()->name(),
            'contato' => fake()->name(),
            'telefone' => $this->gerarTelefoneCelular(),
            'email' => fake()->unique()->safeEmail(),
        ];
    }

    private function gerarTelefoneCelular(): string
    {
        $ddd = random_int(11, 99);

        $numero = '9' . random_int(10000000, 99999999);

        return (string) $ddd . $numero;
    }
}
