<?php

namespace App\Services;

use App\Models\Client;
use Exception;
use Str;

class ClientService
{
    public function create(array $data)
    {
        // 1. Limpeza do Telefone
        $data['telefone'] = $this->limparTelefone($data['telefone']);

        // 2. Validação do Telefone
        $this->validateTelefone($data['telefone']);

        return Client::create($data);
    }

    public function update(Client $client, array $data)
    {
        // 1. Limpeza do Telefone
        $data['telefone'] = $this->limparTelefone($data['telefone']);

        // 2. Validação do Telefone
        $this->validateTelefone($data['telefone']);

        return $client->update($data);
    }

    public function delete(Client $client)
    {
        if ($client->servicos()->withTrashed()->exists()) {
            throw new Exception("Não se pode deletar um cliente com serviço vinculado.");
        }

        return $client->delete();
    }

    private function limparTelefone($phone)
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    private function validateTelefone($phone)
    {
        // 1. Validação de tamanho
        if (Str::of($phone)->length() !== 11) {
            throw new Exception("O telefone deve conter 11 dígitos.");
        }

        // 2. Validação de unicidade
        // $query = Client::where('telefone', $phone);

        // if ($ignoreId) {
        //     $query->where('id', '!=', $ignoreId);
        // }

        // if ($query->exists()) {
        //     throw new Exception("O telefone já foi cadastrado.");
        // }
    }
}
