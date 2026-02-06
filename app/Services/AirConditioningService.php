<?php

namespace App\Services;

use App\Models\AirConditioning;
use DB;
use Exception;
use Http;

class AirConditioningService
{
    public function create(array $data, array $address)
    {
        $data['codigo_ac'] = AirConditioning::gerarCodigo($data['cliente_id']);

        return DB::transaction(function() use ($data, $address) {
            $ac = AirConditioning::create($data);
            $ac->address()->create($address);
            return $ac;
        });
    }

    public function update(AirConditioning $ac, array $data, array $address)
    {
        return DB::transaction(function() use ($ac, $data, $address) {
            $ac->update($data);
            $ac->address()->updateOrCreate([], $address);
            return $ac;
        });
    }

    public function delete(AirConditioning $ac)
    {
        if ($ac->servicos()->withTrashed()->exists()) {
            throw new Exception('Não se pode deletar um ar-condicionado com serviço vinculado.');
        }

        return DB::transaction(function() use ($ac) {
            // 1. Deleta o endereço vinculado.
            $ac->address()->delete();

            // 2. Deleta o equipamento.
            return $ac->delete();
        });
    }

    public function loadCep($value)
    {
        // 1. Limpa o cep
        $cep = preg_replace('/[^0-9]/', '', $value);

        // 2. Validação de tamanho
        if (strlen($cep) != 8) {
            throw new Exception('O CEP deve ter 8 dígitos.');
        }

        // 3. Busca da API
        $response = Http::withOptions([
            'verify' => true,
        ])
        ->withUserAgent('MjEngenharia')
        ->timeout(10)
        ->get("https://viacep.com.br/ws/{$cep}/json/");

        // 4. Retorna a resposta
        return $response;
    }
}
