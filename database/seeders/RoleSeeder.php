<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Adiciona os Perfis.
        $roleAdmin = Role::firstOrCreate(['name' => 'adm']);
        $roleExecutor = Role::firstOrCreate(['name' => 'executor']);
        $roleAssistente = Role::firstOrCreate(['name' => 'assistente']);

        // 2. Atribui as Permissões.

    }
}
