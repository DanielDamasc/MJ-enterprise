<?php

namespace App\Livewire;

use App\Enums\ServiceStatus;
use App\Models\OrderService;
use Carbon\Carbon;
use Exception;
use Livewire\Attributes\Layout;
use Livewire\Component;

class EmployeeServicesManager extends Component
{
    protected function nextSanitation($value)
    {
        return Carbon::parse($value)->addDays(180);
    }

    public function concluirService($id)
    {
        $service = OrderService::where('id', $id)
            ->where('executor_id', auth()->id())
            ->first();

        if (!$service) {
            $this->dispatch('notify-error', 'Serviço não encontrado!');
            return ;
        }

        try {
            $service->concluir();
            $this->dispatch('notify-success', 'Serviço concluído com sucesso!');
        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $user = auth()->user();

        $services = OrderService::with(['air_conditioner.address', 'client'])
            ->where('executor_id', $user->id)
            ->where('status', ServiceStatus::AGENDADO->value)
            ->orderBy('data_servico', 'asc')
            ->get();

        return view('livewire.employee-services-manager', [
            'services' => $services
        ]);
    }
}
