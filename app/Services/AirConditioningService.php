<?php

namespace App\Services;

use App\Models\AirConditioning;
use DB;
use Exception;

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
}
