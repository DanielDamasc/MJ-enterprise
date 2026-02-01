<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'cep' => fake()->postcode(),
            'rua' => fake()->streetName(),
            'numero' => fake()->buildingNumber(),
            'bairro' => ucfirst(fake()->words(2, true)),
            'complemento' => fake()->optional(0.4)->randomElement(['Ap. 201', 'Ap. 301', 'Ap. 401']),
            'cidade' => fake()->city(),
            'uf' => fake()->randomElement(['MG', 'SP', 'RJ']),

            // Quando chama via relacionamento o Laravel preenche sozinho.
            'addressable_id' => null,
            'addressable_type' => null,
        ];
    }
}
