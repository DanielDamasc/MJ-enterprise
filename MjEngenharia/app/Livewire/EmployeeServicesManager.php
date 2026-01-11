<?php

namespace App\Livewire;

use App\Enums\ServiceStatus;
use App\Models\OrderService;
use Livewire\Attributes\Layout;
use Livewire\Component;

class EmployeeServicesManager extends Component
{
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
