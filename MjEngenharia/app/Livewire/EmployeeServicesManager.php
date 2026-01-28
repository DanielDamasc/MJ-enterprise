<?php

namespace App\Livewire;

use App\Enums\ServiceStatus;
use App\Models\OrderService;
use DB;
use Exception;
use Livewire\Attributes\Layout;
use Livewire\Component;

class EmployeeServicesManager extends Component
{
    // --- Modais e Visualização ---
    public $showEquipmentsModal = false;
    public $showFinishModal = false;

    // --- Dados do Serviço ---
    public $selectedService = null;
    public array $selectedEquipmentsIds = [];

    // --- Dados para Finalização do Serviço ---
    public $observacoes_executor = '';
    public $idsFinais = [];
    public $idServicoFinal = null;
    public $isPartial = false; // bool

    public function showEquipments($serviceId)
    {
        $this->selectedService = OrderService::with(['airConditioners' => function($query) {
            $query->orderBy('codigo_ac');
        }])->find($serviceId);

        $this->selectedEquipmentsIds = $this->selectedService->airConditioners->pluck('id')->toArray();

        $this->showEquipmentsModal = true;
    }

    public function closeEquipments()
    {
        $this->showEquipmentsModal = false;
        $this->selectedService = null;
    }

    public function closeFinishModal()
    {
        $this->showFinishModal = false;
        $this->observacoes_executor = '';
        $this->idsFinais = [];
        $this->idServicoFinal = null;
        $this->isPartial = false;
    }

    public function concluirTotal($serviceId)
    {
        $service = OrderService::with('airConditioners')
            ->where('id', $serviceId)
            ->where('executor_id', auth()->id())
            ->first();

        if (!$service) {
            $this->dispatch('notify-error', 'Serviço não encontrado.');
            return ;
        }

        $allIds = $service->airConditioners->pluck('id')->toArray();

        // Usa todos os IDs, conclusão comum.
        $this->showConfirmacao($serviceId, $allIds, false);
    }

    public function concluirParcial()
    {
        if (!$this->selectedService) {
            return ;
        }

        // Validando se algum equipamento foi selecionado.
        if (empty($this->selectedEquipmentsIds)) {
            $this->dispatch('notify-error', 'Selecione pelo menos um equipamento.');
            return ;
        }

        // Se a quantidade de equipamentos atendidos for menor que o total, o serviço é parcial.
        $totalEquipamentos = $this->selectedService->airConditioners->count();
        $totalSelecionados = count($this->selectedEquipmentsIds);
        $isPartial = $totalSelecionados < $totalEquipamentos;

        // Usa apenas os IDs que o usuário marcou.
        $this->showConfirmacao($this->selectedService->id, $this->selectedEquipmentsIds, $isPartial);
    }

    private function showConfirmacao($serviceId, $ids, $isPartial)
    {
        $this->idServicoFinal = $serviceId;
        $this->idsFinais = $ids;
        $this->isPartial = $isPartial;
        $this->observacoes_executor = ''; // Limpa observações anteriores.

        $this->showFinishModal = true;
    }

    public function finalizarServico()
    {
        $service = OrderService::with('airConditioners')
            ->where('id', $this->idServicoFinal)
            ->where('executor_id', auth()->id())
            ->first();

        if ($service) {
            $this->processarConclusao($service, $this->idsFinais);
        } else {
            $this->dispatch('notify-error', 'Erro ao processar serviço.');
            $this->closeFinishModal();
        }
    }

    private function processarConclusao(OrderService $service, array $ids)
    {
        try {
            DB::transaction(function () use ($service, $ids) {

                $pivotData = [];
                $novoTotal = 0;

                // 1. Atualiza a pivot para manter somente os equipamentos selecionados.
                // Recalcula o novo total do serviço considerando somente os selecionados.
                foreach ($service->airConditioners as $ac) {
                    if (in_array($ac->id, $ids)) {
                        $pivotData[$ac->id] = ['valor' => $ac->pivot->valor];
                        $novoTotal += $ac->pivot->valor;
                    }
                }
                $service->airConditioners()->sync($pivotData);

                // 2. Atualiza o serviço com o novo total.
                $service->update([
                    'total' => $novoTotal,
                    'observacoes_executor' => $this->observacoes_executor,
                ]);

                // 3. Chama o método para realmente concluir o serviço, considerando somente os equipamentos selecionados.
                $service->concluir();
            });

            $this->dispatch('notify-success', 'Serviço concluído com sucesso!');

        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());

        } finally {
            // Fecha os modais.
            $this->closeFinishModal();
            $this->closeEquipments();
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $user = auth()->user();

        $services = OrderService::with(['airConditioners.address', 'client'])
            ->where('executor_id', $user->id)
            ->where('status', ServiceStatus::AGENDADO->value)
            ->orderBy('data_servico', 'asc')
            ->get();

        return view('livewire.employee-services-manager', [
            'services' => $services
        ]);
    }
}
