<?php

namespace App\Livewire;

use App\Enums\ServiceStatus;
use App\Models\AirConditioning;
use App\Models\Client;
use App\Models\OrderService;
use App\Models\User;
use App\Services\ServiceService;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class ServicesManager extends Component
{
    protected ServiceService $serviceService;

    public function boot(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public $serviceId = '';
    // Chaves Estrangeiras.
    public array $ac_ids = [];

    public $acs_disponiveis = [];

    public $cliente_id = '';
    public $executor_id = '';

    // Atributos Normais.
    public $tipo = '';
    public $data_servico = '';
    public $horario = '';
    public array $ac_precos = [];
    public $valor_total = 0;
    public $status = ServiceStatus::AGENDADO->value;
    public $observacoes_executor = '';
    public array $detalhes = [];

    // Outros Atributos.
    public $showView = false;
    public $showCreate = false;
    public $showDelete = false;
    public $showEdit = false;
    public $showConfirm = false;
    public $showCancel = false;

    // Atributos Auxiliares.
    public $cliente_label = '';
    public $status_label = '';
    public $tipo_label = '';
    public $executor_label = '';

    protected function rules()
    {
        $rules = [
            'cliente_id' => 'required|integer|exists:clients,id',
            'executor_id' => 'required|integer|exists:users,id',

            'ac_ids' => 'required|array|min:1',
            'ac_ids.*' => 'exists:air_conditioners,id',

            'horario' => 'nullable|date_format:H:i', // HH:MM

            'tipo' => 'required|string',
            'ac_precos' => 'required|array|min:1',
            'ac_precos.*' => 'required|numeric|min:0',
            'status' => ['required', new Enum(ServiceStatus::class)],
        ];

        if ($this->tipo == 'higienizacao') {
            $rules['detalhes.limpou_condensadora'] = 'boolean';
        }

        if ($this->status == ServiceStatus::AGENDADO->value) {
            $rules['data_servico'] = 'required|date|after_or_equal:today';
        }
        elseif (in_array($this->status, [ServiceStatus::CONCLUIDO->value, ServiceStatus::CANCELADO->value])) {
            $rules['data_servico'] = 'required|date|before_or_equal:today';
        }
        else {
            $rules['data_servico'] = 'required|date';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'ac_ids.required' => 'Selecione um ar-condicionado.',
            'ac_ids.min' => 'Selecione pelo menos um ar-condicionado.',

            'data_servico.after_or_equal' => 'Para agendamentos, a data não pode ser no passado.',
            'data_servico.before_or_equal' => 'Para serviços concluídos ou cancelados, a data não pode ser no futuro.',
        ];
    }

    public function updatedClienteId($value)
    {
        $this->reset('ac_ids', 'acs_disponiveis', 'ac_precos');

        if ($value) {
            $this->acs_disponiveis = AirConditioning::where('cliente_id', $value)->get();
        }
    }

    public function closeModal()
    {
        $this->showView = $this->showCreate = $this->showDelete = $this->showEdit = $this->showConfirm = $this->showCancel = false;
        $this->resetValidation();
    }

    private function fillFields($id)
    {
        $service = OrderService::with('airConditioners')->find($id);

        if ($service) {
            $this->acs_disponiveis = AirConditioning::where('cliente_id', $service->cliente_id)->get();
            $this->cliente_label = $service->client->cliente ?? 'Cliente não encontrado';
            $this->status_label = $service->status->label();
            $this->tipo_label = $service->tipo;

            $this->cliente_id = $service->cliente_id;
            $this->executor_id = $service->executor_id;
            $this->tipo = $service->tipo;
            $this->data_servico = $service->data_servico;
            $this->horario = $service->horario ?? '';
            $this->status = $service->status->value;
            $this->detalhes = $service->detalhes;

            $this->ac_ids = $service->airConditioners->pluck('id')->toArray();

            // Iterando na relation e pegando os valores da pivot.
            $this->ac_precos = [];
            $this->valor_total = 0;
            foreach ($service->airConditioners as $ac) {
                $this->ac_precos[$ac->id] = $ac->pivot->valor;
                $this->valor_total += $ac->pivot->valor;
            }

            // Apenas para a visualização.
            $this->executor_label = $service->user->name ?? 'Executor não encontrado';
            $this->observacoes_executor = $service->observacoes_executor ?? null;
        }
    }

    #[On('show')]
    public function show($id)
    {
        $this->serviceId = $id;
        $this->showView = true;

        if ($this->serviceId) {
            $this->fillFields($this->serviceId);
        }
    }

    public function openCreate()
    {
        $this->reset([
            'ac_ids',
            'acs_disponiveis',
            'cliente_id',
            'executor_id',
            'tipo',
            'data_servico',
            'horario',
            'ac_precos',
            'status',
            'detalhes',
        ]);
        $this->resetValidation();
        $this->showCreate = true;
    }

    public function save()
    {
        $this->validate();

        try {
            $this->serviceService->create(
                $this->ac_ids,
                $this->ac_precos,
                [
                'cliente_id' => $this->cliente_id,
                'executor_id' => $this->executor_id,
                'tipo' => $this->tipo,
                'data_servico' => $this->data_servico,
                'horario' => $this->horario, // casta para null na model.
                'status' => $this->status,
                'detalhes' => $this->detalhes,
                'total' => null
            ]);

            $this->closeModal();
            $this->dispatch('notify-success', 'Ordem de Serviço criada com sucesso!');
            $this->dispatch('service-refresh');

        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());

        }
    }

    #[On('open-edit')]
    public function openEdit($id)
    {
        $this->serviceId = $id;
        $this->showEdit = true;

        if ($this->serviceId) {
            $this->fillFields($this->serviceId);
        }
    }

    #[On('edit')]
    public function edit()
    {
        $this->validate();

        $service = OrderService::find($this->serviceId);

        try {
            $this->serviceService->update(
                $service,
                $this->ac_ids,
                $this->ac_precos,
            [
                'executor_id' => $this->executor_id,
                'data_servico' => $this->data_servico,
                'horario' => $this->horario, // casta para null na model.
                'detalhes' => $this->detalhes,
                'total' => null
            ]);

            $this->closeModal();
            $this->dispatch('notify-success', 'Dados atualizados com sucesso!');
            $this->dispatch('service-refresh');

        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());

        } finally {
            $this->serviceId = null;

        }
    }

    #[On('confirm-delete')]
    public function confirmDelete($id)
    {
        $this->serviceId = $id;
        $this->showDelete = true;
    }

    #[On('delete')]
    public function delete()
    {
        if ($this->serviceId) {
            $service = OrderService::find($this->serviceId);

            try {
                $this->serviceService->delete($service);
                $this->dispatch('notify-success', 'Serviço movido para a lixeira.');
                $this->dispatch('service-refresh');

            } catch (Exception $e) {
                $this->dispatch('notify-error', $e->getMessage());

            } finally {
                $this->serviceId = null;
                $this->closeModal();

            }
        }
    }

    #[On('confirm-service-done')]
    public function confirmServiceDone($id)
    {
        $this->serviceId = $id;
        $this->showConfirm = true;
    }

    #[On('done')]
    public function serviceDone()
    {
        if ($this->serviceId) {
            $service = OrderService::find($this->serviceId);

            if (!$service) {
                $this->dispatch('notify-error', 'Serviço não encontrado.');
                return ;
            }

            try {
                $service->concluir();
                $this->dispatch('notify-success', 'Serviço concluído com sucesso!');
            } catch (Exception $e) {
                $this->dispatch('notify-error', $e->getMessage());
            }
        }

        $this->closeModal();
        $this->serviceId = null;

        $this->dispatch('service-refresh');
    }

    #[On('confirm-service-cancel')]
    public function confirmServiceCancel($id)
    {
        $this->serviceId = $id;
        $this->showCancel = true;
    }

    #[On('cancel')]
    public function serviceCancel()
    {
        if ($this->serviceId) {
            $service = OrderService::find($this->serviceId);

            if (!$service) {
                $this->dispatch('notify-error', 'Serviço não encontrado.');
                return ;
            }

            try {
                $this->serviceService->cancel($service);
                $this->dispatch('notify-success', 'Ordem de serviço cancelada.');
                $this->dispatch('service-refresh');

            } catch (Exception $e) {
                $this->dispatch('notify-error', $e->getMessage());

            } finally {
                $this->serviceId = null;
                $this->closeModal();

            }
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $clientes = Client::get();
        $executores = User::role('executor')->get();
        $statusServico = ServiceStatus::cases();

        return view('livewire.services-manager', [
            'clientes' => $clientes,
            'executores' => $executores,
            'statusServico' => $statusServico
        ]);
    }
}
