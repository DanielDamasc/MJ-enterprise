<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeService
{
    public function create(array $data, string $perfil): User
    {
        return DB::transaction(function() use ($data, $perfil) {
            // Geração de senha aleatória ao criar conta.
            if (!isset($data['password'])) {
                $data['password'] = Hash::make(Str::random(40));
            }

            // Cria o user e atribui o role.
            $employee = User::create($data);

            // Atribui o perfil.
            if ($perfil) {
                $employee->assignRole($perfil);
            }

            return $employee;
        });
    }

    public function update(User $employee, array $data, string $perfil): bool
    {
        $employee->syncRoles($perfil);
        return $employee->update($data);
    }

    public function delete(User $employee): ?bool
    {
        if ($employee->servicos()->withTrashed()->exists()) {
            throw new Exception('Não se pode deletar um executor com serviço vinculado.');
        }

        $employee->syncRoles([]);
        return $employee->delete();
    }
}
