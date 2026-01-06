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
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'dandamasceno04@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('asdasdasd'),
            'remember_token' => Str::random(10),
        ]);

        $admin->assignRole('adm');
    }
}
