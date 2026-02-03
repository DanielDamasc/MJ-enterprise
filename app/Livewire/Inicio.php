<?php

namespace App\Livewire;

use App\Models\AirConditioning;
use App\Models\Client;
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

    #[Layout('layouts.app')]
    public function render()
    {
        // As computed properties ficam disponíveis automaticamente na view.
        // Modo mais simples, sem execesso de requisições.
        return view('livewire.inicio');
    }
}
