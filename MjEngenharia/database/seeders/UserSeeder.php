<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (env('DEV_ADMIN_EMAIL') && env('DEV_ADMIN_PASSWORD')) {
            $dev = User::firstOrCreate(
                ['email' => env('DEV_ADMIN_EMAIL')],
                [
                    'name' => env('DEV_ADMIN_NAME', 'Desenvolvedor'),
                    'email_verified_at' => now(),
                    'password' => Hash::make(env('DEV_ADMIN_PASSWORD')),
                ]
            );
            $dev->assignRole('adm');
        }

        if (env('OWNER_ADMIN_EMAIL') && env('OWNER_ADMIN_PASSWORD')) {
            $owner = User::firstOrCreate(
                ['email' => env('OWNER_ADMIN_EMAIL')],
                [
                    'name' => env('OWNER_ADMIN_NAME', 'Administrador'),
                    'email_verified_at' => now(),
                    'password' => Hash::make(env('OWNER_ADMIN_PASSWORD')),
                ]
            );
            $owner->assignRole('adm');
        }
    }
}
