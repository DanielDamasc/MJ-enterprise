<?php

namespace Database\Factories;

use App\Enums\ServiceStatus;
use App\Models\Client;
use App\Models\OrderService;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderService>
 */

class OrderServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = OrderService::class;

    public function definition(): array
    {
        return [
            'cliente_id' => Client::factory(),

            'executor_id' => User::factory()->afterCreating(function ($user) {
                // Cria a role se ela não existir, ou pega a existente
                $role = Role::firstOrCreate(['name' => 'executor', 'guard_name' => 'web']);

                $user->assignRole($role);
            }),

            'tipo' => fake()->randomElement(['higienizacao', 'instalacao', 'manutencao', 'carga_gas', 'correcao_vazamento']),
            'data_servico' => fake()->dateTimeBetween('now', '+1 month'),
            'status' => ServiceStatus::AGENDADO,
            'total' => 0,
            'observacoes_executor' => null,
            'detalhes' => [],
        ];
    }
}
