<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\AirConditioning;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AirConditioning>
 */
class AirConditioningFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = AirConditioning::class;

    public function definition(): array
    {
        return [
            'cliente_id' => Client::factory(),
            'codigo_ac' => 'AC' . fake()->unique()->numberBetween(1, 99),
            'ambiente' => fake()->randomElement([
                'Sala de Estar', 'Quarto Principal', 'Escritório',
                'Recepção', 'Sala de Reunião'
            ]),
            'modelo' => fake()->bothify('Mod-##??'),
            'marca' => fake()->randomElement([
                'LG', 'Samsung', 'Carrier',
                'Elgin', 'Consul'
            ]),
            'potencia' => fake()->randomElement([
                9000, 12000, 18000, 24000, 30000, 36000, 48000, 60000
            ]),
            'tipo' => fake()->randomElement([
                'hw', 'k7', 'piso_teto'
            ]),

            // data da próxima higienização
            'prox_higienizacao' => fake()->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (AirConditioning $ac) {
            $ac->address()->create(
              Address::factory()->make()->toArray()
            );
        });
    }
}
