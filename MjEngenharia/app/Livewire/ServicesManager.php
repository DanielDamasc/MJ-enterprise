<?php

namespace App\Livewire;

use App\Enums\ServiceStatus;
use App\Models\AirConditioning;
use App\Models\Client;
use App\Models\OrderService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class ServicesManager extends Component
{
    public $serviceId = '';
    // Chaves Estrangeiras.
    public array $ac_ids = [];

    public $acs_disponiveis = [];

    public $cliente_id = '';
    public $executor_id = '';

    // Atributos Normais.
    public $tipo = '';
    public $data_servico = '';
    public $valor = '';
    public $status = ServiceStatus::AGENDADO->value;
    public array $detalhes = [];

    // Outros Atributos.
    public $showCreate = false;
    public $showDelete = false;
    public $showEdit = false;
    public $showConfirm = false;

    // Atributos Auxiliares.
    public $cliente_label = '';
    public $status_label = '';
    public $tipo_label = '';

    protected function rules()
    {
        $rules = [
            'cliente_id' => 'required|integer|exists:clients,id',
            'executor_id' => 'required|integer|exists:users,id',

            'ac_ids' => 'required|array|min:1',
            'ac_ids.*' => 'exists:air_conditioners,id',

            'tipo' => 'required|string',
            'valor' => 'required|numeric|min:0',
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
        $this->reset('ac_ids', 'acs_disponiveis');

        if ($value) {
            $this->acs_disponiveis = AirConditioning::where('cliente_id', $value)->get();
        }
    }

    protected function nextSanitation($value)
    {
        return Carbon::parse($value)->addDays(180);
    }

    public function closeModal()
    {
        $this->showCreate = $this->showDelete = $this->showEdit = $this->showConfirm = false;
        $this->resetValidation();
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
            'valor',
            'status',
            'detalhes',
        ]);
        $this->resetValidation();
        $this->showCreate = true;
    }

    public function save()
    {
        $this->validate();

        foreach ($this->ac_ids as $acId) {
            OrderService::create([
                'ac_id' => $acId,
                'cliente_id' => $this->cliente_id,
                'executor_id' => $this->executor_id,

                'tipo' => $this->tipo,
                'data_servico' => $this->data_servico,
                'valor' => $this->valor,
                'status' => $this->status,
                'detalhes' => $this->detalhes
            ]);

            // Atualiza a data da próxima higienização se o serviço atual estiver concluído.
            if ($this->tipo == 'higienizacao' && $this->status == ServiceStatus::CONCLUIDO->value) {
                $ac = AirConditioning::find($acId);
                if ($ac) {
                    $ac->prox_higienizacao = $this->nextSanitation($this->data_servico);
                    $ac->save();
                }
            }

            $this->closeModal();
            $this->dispatch('service-refresh');
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

            if ($service) {
                if ($service->status == ServiceStatus::CONCLUIDO) {
                    $this->dispatch('error', 'Não é possível excluir um serviço já finalizado!');
                    return ;
                }

                $service->delete();
                session()->flash('message', 'Ordem de Serviço deletada com sucesso.');
                $this->dispatch('notify', 'Serviço movido para a lixeira.');
            }
        }

        $this->closeModal();
        $this->serviceId = null;

        $this->dispatch('service-refresh');
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
                $this->dispatch('error', 'Serviço não encontrado.');
                return ;
            }

            // Valida se a data permite que o serviço seja concluído.
            if (Carbon::parse($service->data_servico)->startOfDay()->isFuture()) {
                $this->dispatch('error', 'Não é possível finalizar um serviço agendado para o futuro.');
                return ;
            }

            // Verifica se o status é agendado.
            if ($service->status->value == ServiceStatus::AGENDADO->value) {
                $service->update([
                    'status' => ServiceStatus::CONCLUIDO->value
                ]);

                // Atualiza a data da próxima higienização.
                if ($service->tipo == 'higienizacao') {
                    $ac = $service->air_conditioner;
                    if ($ac) {
                        $ac->prox_higienizacao = $this->nextSanitation($service->data_servico);
                        $ac->save();
                    }
                }

                session()->flash('message', 'Ordem de serviço concluída');
                $this->dispatch('notify', 'Ordem de serviço concluída.');
            } else {
                $this->dispatch('error', 'Apenas serviços agendados podem ser concluídos.');
            }
        }

        $this->closeModal();
        $this->serviceId = null;

        $this->dispatch('service-refresh');
    }

    #[On('open-edit')]
    public function openEdit($id)
    {
        $this->serviceId = $id;
        $this->showEdit = true;

        if ($this->serviceId) {
            $service = OrderService::find($this->serviceId);

            $this->acs_disponiveis = AirConditioning::where('cliente_id', $service->cliente_id)->get();
            $this->cliente_label = $service->client->cliente ?? 'Cliente não encontrado';
            $this->status_label = $service->status->label();
            $this->tipo_label = $service->tipo;

            $this->ac_ids = [$service->ac_id];
            $this->cliente_id = $service->cliente_id;
            $this->executor_id = $service->executor_id;
            $this->tipo = $service->tipo;
            $this->data_servico = $service->data_servico;
            $this->valor = $service->valor;
            $this->status = $service->status;
            $this->detalhes = $service->detalhes;
        }
    }

    #[On('edit')]
    public function edit()
    {
        $this->validate();

        $service = OrderService::find($this->serviceId);

        // Verifica se o status é agendado.
        if ($service->status === ServiceStatus::AGENDADO) {

            $service->update([
                // Somente estes atributos podem ser editados.
                'executor_id' => $this->executor_id,
                'data_servico' => $this->data_servico,
                'valor' => $this->valor,
                'detalhes' => $this->detalhes,
            ]);

            session()->flash('message', 'Dados atualizados com sucesso!');
            $this->dispatch('notify', 'Dados atualizados com sucesso!');
        } else {
            $this->dispatch('error', 'Apenas serviços agendados podem ser editados.');
        }

        $this->closeModal();
        $this->serviceId = null;

        $this->dispatch('service-refresh');
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
