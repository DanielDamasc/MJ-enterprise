<?php

namespace App\Services;

use App\Models\User;
use DB;
use Exception;
use Hash;
use Str;

class EmployeeService
{
    public function create(array $data): User
    {
        return DB::transaction(function() use ($data) {
            // Geração de senha aleatória ao criar conta
            if (!isset($data['password'])) {
                $data['password'] = Hash::make(Str::random(40));
            }

            $employee = User::create($data);
            $employee->assignRole('executor');
            return $employee;
        });
    }

    public function update(User $employee, array $data): bool
    {
        return $employee->update($data);
    }

    public function delete(User $employee): ?bool
    {
        if ($employee->servicos()->withTrashed()->exists()) {
            throw new Exception('Não se pode deletar um executor com serviço vinculado.');
        }

        return $employee->delete();
    }
}
