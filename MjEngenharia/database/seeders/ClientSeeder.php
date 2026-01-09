<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::create([
            'cliente' => 'Tatiane Meira',
            'contato' => 'Tatiane',
            'telefone' => '38999999999',
            'email' => 'tatiane@gmail.com'
        ]);

        Client::create([
            'cliente' => 'Kênia Rocha',
            'contato' => 'Daniel',
            'telefone' => '38988888888',
            'email' => null
        ]);
    }
}
