<?php

namespace App\Services;

use App\Models\Client;
use DB;
use Exception;
use Str;

class ClientService
{
    public function create(array $data, array $address)
    {
        return DB::transaction(function() use ($data, $address) {
            // 1. Limpeza do Telefone
            $data['telefone'] = $this->limparTelefone($data['telefone']);

            // 2. Validação do Telefone
            $this->validateTelefone($data['telefone']);

            // 3. Cria o cliente com os dados
            $cliente = Client::create($data);

            // 4. Se o cep não for vazio, cria o endereço do cliente
            if (!empty($address['cep'])) {
                $cliente->address()->create($address);
            }

            return $cliente;
        });
    }

    public function update(Client $client, array $data, array $address)
    {
        return DB::transaction(function() use ($client, $data, $address) {
            // 1. Limpeza do Telefone
            $data['telefone'] = $this->limparTelefone($data['telefone']);

            // 2. Validação do Telefone
            $this->validateTelefone($data['telefone']);

            // 3. Se o cep não for vazio, cria ou atualiza o endereço para o cliente.
            if (!empty($address['cep'])) {
                $client->address()->updateOrCreate(
                    // Busca no banco se existe.
                    [
                        'addressable_id' => $client->id,
                        'addressable_type' => Client::class,
                    ],
                    // Caso exista, atualiza, caso contrário, cria.
                    $address
                );
            } else {
                // Deleta caso o usuário tenha limpado os campos.
                $client->address()->delete();
            }

            return $client->update($data);
        });
    }

    public function delete(Client $client)
    {
        if ($client->servicos()->withTrashed()->exists()) {
            throw new Exception("Não se pode deletar um cliente com serviço vinculado.");
        }

        return DB::transaction(function() use ($client) {
            // Remove o endereço vinculado.
            $client->address()->delete();

            return $client->delete();
        });
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
