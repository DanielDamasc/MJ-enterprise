<?php

namespace App\Livewire;

use App\Enums\ServiceStatus;
use App\Models\AirConditioning;
use App\Models\Client;
use App\Models\OrderService;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Inicio extends Component
{
    #[Computed]
    public function totalClients()
    {
        return Client::count();
    }

    #[Computed]
    public function totalEmployees()
    {
        return User::role(['executor', 'assistente'])->count();
    }

    #[Computed]
    public function totalACs()
    {
        return AirConditioning::count();
    }

    #[Computed]
    public function totalServicosAgendados()
    {
        return OrderService::where('status', ServiceStatus::AGENDADO)
            ->count();
    }

    #[Computed]
    public function totalServicosConcluidos()
    {
        return OrderService::where('status', ServiceStatus::CONCLUIDO)
            ->count();
    }

    #[Computed]
    public function totalServicosCancelados()
    {
        return OrderService::where('status', ServiceStatus::CANCELADO)
            ->count();
    }

    #[Computed]
    public function totalFaturamento()
    {
        $valores = OrderService::where('status', ServiceStatus::CONCLUIDO)
            ->pluck('total');

        $total = 0;
        foreach ($valores as $valor) {
            $total += $valor;
        }

        return $total;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        // As computed properties ficam disponíveis automaticamente na view.
        // Modo mais simples, sem execesso de requisições.
        return view('livewire.inicio');
    }
}
