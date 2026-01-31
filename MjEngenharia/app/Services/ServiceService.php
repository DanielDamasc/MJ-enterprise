<?php

namespace App\Services;

use App\Enums\ServiceStatus;
use App\Models\OrderService;
use DB;
use Exception;

class ServiceService
{
    public function create(array $acIds, array $acPrecos, array $data)
    {
        return DB::transaction(function() use ($acIds, $acPrecos, $data) {
            // 1. Prepara os dados da pivô e calcula o preço total do serviço.
            $calculo = $this->calculaPivo($acIds, $acPrecos);
            $data['total'] = $calculo['total'];

            // 2. Cria a OS.
            $os = OrderService::create($data);

            // 3. Vincula atráves da tabela pivô, adiciona os preços unitários.
            $os->airConditioners()->attach($calculo['pivot']);

            // 4. Atualiza a próxima higienização se for concluído.
            if (in_array($os->tipo, ['higienizacao', 'instalacao']) && $os->status == ServiceStatus::CONCLUIDO) {
                $proxData = $os->proximaHigienizacao($os->data_servico);

                $os->airConditioners()->update([
                    'prox_higienizacao' => $proxData
                ]);
            }

            return $os;
        });
    }

    public function update(OrderService $orderService, array $acIds, array $acPrecos, array $data)
    {
        // Edição só pode ser feita em serviços agendados.
        if ($orderService->status === ServiceStatus::AGENDADO) {

            DB::transaction(function () use ($orderService, $acIds, $acPrecos, $data) {
                // 1. Prepara os dados da pivô e calcula o preço total do serviço.
                $calculo = $this->calculaPivo($acIds, $acPrecos);
                $data['total'] = $calculo['total'];

                // 2. Atualiza a OS.
                $orderService->update($data);

                // 3. Atualiza os dados da tabela pivô.
                $orderService->airConditioners()->sync($calculo['pivot']);
            });


        } else {
            throw new Exception('Apenas serviços agendados podem ser editados.');

        }
    }

    public function delete(OrderService $orderService)
    {
        if ($orderService->status == ServiceStatus::CONCLUIDO) {
            throw new Exception('Não é possível excluir um serviço já finalizado.');
        }

        $orderService->delete();
    }

    public function cancel(OrderService $orderService)
    {
        // 1. O serviço só pode ser cancelado se ele estiver agendado.
        if ($orderService->status == ServiceStatus::AGENDADO) {

            DB::transaction(function () use ($orderService) {
                // 2. Cancela o status da OS.
                $orderService->update([
                    'status' => ServiceStatus::CANCELADO->value
                ]);

                // 3. Regra para INSTALAÇÃO, caso o serviço seja cancelado, deleta os AC.
                $this->deleteAC($orderService);
            });

        } else {
            throw new Exception('Apenas serviços agendados podem ser cancelados.');

        }
    }

    private function calculaPivo(array $acIds, array $acPrecos)
    {
        // Reseta as variáveis.
        $pivotData = [];
        $total = 0;

        // Prepara os dados para o attach e calcula o total.
        foreach ($acIds as $acId) {
            if (isset($acPrecos[$acId]) && $acPrecos[$acId] != '') {
                $preco = (float) $acPrecos[$acId];
            } else {
                $preco = 0;
            }
            $pivotData[$acId] = ['valor' => $preco];
            $total += $preco;
        }

        return [
            'pivot' => $pivotData,
            'total' => $total
        ];
    }

    private function deleteAC(OrderService $orderService)
    {
        if ($orderService->tipo == 'instalacao') {
            foreach ($orderService->airConditioners as $ac) {
                // Apaga somente se tiver este único serviço relacionado.
                if ($ac->servicos()->count() <= 1) {
                    // Apaga o vínculo com a tabela pivot antes de deletar.
                    $ac->servicos()->detach();

                    // Deleta o AC e o endereço vinculado.
                    $ac->delete();
                    $ac->address?->delete();
                }
            }
        }
    }
}
