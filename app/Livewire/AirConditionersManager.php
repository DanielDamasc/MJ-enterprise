<?php

namespace App\Livewire;

use App\Models\AirConditioning;
use App\Models\Client;
use App\Models\User;
use App\Services\AirConditioningService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class AirConditionersManager extends Component
{
    protected AirConditioningService $acService;

    public function boot(AirConditioningService $acService)
    {
        $this->acService = $acService;
    }

    // Atributos de Ar-condicionado.
    public $cliente_id = null;
    public $prox_higienizacao = '';
    public $ambiente = '';
    public $modelo = '';
    public $marca = '';
    public $potencia = 0;
    public $tipo = '';
    public $tipo_gas = '';
    public $codigo_ac = '';

    // Atributos de Endereço.
    public $cep = '';
    public $rua = '';
    public $numero = '';
    public $bairro = '';
    public $complemento = '';
    public $cidade = '';
    public $uf;

    // Outros Atributos.
    public $showCreate = false;
    public $showDelete = false;
    public $showEdit = false;
    public $equipmentId = null;

    protected function rules()
    {
        return [
            'cliente_id' => 'required|integer|exists:clients,id',
            'ambiente' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'marca' => 'nullable|string|max:50',
            'potencia' => 'required|integer|min:1',
            'tipo_gas' => 'nullable|string|max:50',

            // Administrador tem a liberdade de adicionar a data da próxima higienização
            'prox_higienizacao' => 'nullable|date',

            'tipo' => ['required', Rule::in(['hw', 'k7', 'piso_teto'])],

            'cep' => 'required|string|max:9',
            'rua' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'bairro' => 'required|string|max:100',
            'complemento' => 'nullable|string|max:150',
            'cidade' => 'required|string|max:100',
            'uf' => 'required|string|size:2',
        ];
    }

    public function messages()
    {
        return [
            'cliente_id.required' => 'O campo cliente é obrigatório.',
            'potencia.required' => 'O campo potencia é obrigatório.',
            'tipo.required' => 'O campo tipo é obrigatório.',

            'cep.required' => 'O campo cep é obrigatório.',
            'numero.required' => 'O campo numero é obrigatório.',
        ];
    }

    public function updatedCep($value)
    {
        try {
            $res = $this->acService->loadCep($value);

            if ($res->successful() && !isset($res['erro'])) {
                $dados = $res->json();

                $this->rua = $dados['logradouro'];
                $this->bairro = $dados['bairro'];
                $this->cidade = $dados['localidade'];
                $this->uf = $dados['uf'];

                $this->resetValidation(['rua', 'bairro', 'cidade', 'uf']);
            }
        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());
        }
    }

    public function clearAddress()
    {
        $this->reset([
            'cep',
            'rua',
            'numero',
            'bairro',
            'complemento',
            'cidade',
            'uf'
        ]);
    }

    public function updatedClienteId($value)
    {
        // 1. Habilitado somente para o create.
        if ($this->showEdit) {
            return ;
        }

        // 2. Se o usuário marcou a opção "Selecione um cliente..."
        if (empty($value)) {
            $this->clearAddress();
            return ;
        }

        // 3. Busca o cliente e carrega a relation.
        $cliente = Client::with('address')->find($value);

        // 4. Se o cliente tem endereço, preenche os dados.
        if ($cliente && $cliente->address) {
            $this->cep = $cliente->address->cep;
            $this->rua = $cliente->address->rua;
            $this->numero = $cliente->address->numero;
            $this->bairro = $cliente->address->bairro;
            $this->complemento = $cliente->address->complemento ?? '';
            $this->cidade = $cliente->address->cidade;
            $this->uf = $cliente->address->uf;
        } else {
            $this->clearAddress();
        }
    }

    public function closeModal()
    {
        $this->showCreate = $this->showDelete = $this->showEdit = false;
        $this->resetValidation();
    }

    public function openCreate()
    {
        $this->reset([
            'cliente_id',
            'ambiente',
            'modelo',
            'marca',
            'potencia',
            'prox_higienizacao',
            'tipo',
            'tipo_gas',
            'cep',
            'rua',
            'numero',
            'bairro',
            'complemento',
            'cidade',
            'uf'
        ]);
        $this->resetValidation();
        $this->showCreate = true;
    }

    public function save()
    {
        $this->validate();

        try {
            $this->acService->create(
            [
                'cliente_id' => $this->cliente_id,
                'codigo_ac' => null,
                'ambiente' => $this->ambiente,
                'modelo' => $this->modelo,
                'marca' => $this->marca,
                'potencia' => $this->potencia,
                'tipo' => $this->tipo,
                'tipo_gas' => $this->tipo_gas,
                'prox_higienizacao' => $this->prox_higienizacao ? $this->prox_higienizacao : null
            ],
        [
                'cep' => $this->cep,
                'rua' => $this->rua,
                'numero' => $this->numero,
                'bairro' => $this->bairro,
                'complemento' => $this->complemento,
                'cidade' => $this->cidade,
                'uf' => $this->uf
            ]);

            $this->closeModal();
            $this->dispatch('notify-success', 'Ar-condicionado cadastrado com sucesso!');
            $this->dispatch('airConditioners-refresh');

        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());
        }
    }

    #[On('open-edit')]
    public function openEdit($id)
    {
        $this->equipmentId = $id;
        $this->showEdit = true;

        if ($this->equipmentId) {
            $ac = AirConditioning::with('address')->find($this->equipmentId);
            $this->cliente_id = $ac->cliente_id;
            $this->codigo_ac = $ac->codigo_ac;
            $this->ambiente = $ac->ambiente ?? '';
            $this->modelo = $ac->modelo ?? '';
            $this->marca = $ac->marca ?? '';
            $this->potencia = $ac->potencia;
            $this->tipo = $ac->tipo;
            $this->tipo_gas = $ac->tipo_gas ?? '';

            $this->prox_higienizacao = $ac->prox_higienizacao ?? '';

            $this->cep = $ac->address->cep;
            $this->rua = $ac->address->rua;
            $this->numero = $ac->address->numero;
            $this->bairro = $ac->address->bairro;
            $this->complemento = $ac->address->complemento ?? '';
            $this->cidade = $ac->address->cidade;
            $this->uf = $ac->address->uf;
        }
    }

    public function edit()
    {
        $this->validate();

        $ac = AirConditioning::with('address')->find($this->equipmentId);

        if ($ac) {
            try {
                $this->acService->update($ac,
                [
                    'cliente_id' => $this->cliente_id,
                    'ambiente' => $this->ambiente,
                    'modelo' => $this->modelo,
                    'marca' => $this->marca,
                    'potencia' => $this->potencia,
                    'tipo' => $this->tipo,
                    'tipo_gas' => $this->tipo_gas,
                    'prox_higienizacao' => $this->prox_higienizacao ? $this->prox_higienizacao : null
                ],
            [
                    'cep' => $this->cep,
                    'rua' => $this->rua,
                    'numero' => $this->numero,
                    'bairro' => $this->bairro,
                    'complemento' => $this->complemento,
                    'cidade' => $this->cidade,
                    'uf' => $this->uf
                ]);

                $this->closeModal();
                $this->dispatch('notify-success', 'Dados atualizados com sucesso!');
                $this->dispatch('airConditioners-refresh');

            } catch (Exception $e) {
                $this->dispatch('notify-error', $e->getMessage());

            }
        }
    }

    #[On('confirm-delete')]
    public function confirmDelete($id)
    {
        $this->equipmentId = $id;
        $this->showDelete = true;
    }

    public function delete()
    {
        if ($this->equipmentId) {
            $ac = AirConditioning::find($this->equipmentId);

            try {
                $this->acService->delete($ac);
                $this->dispatch('notify-success', 'Ar-condicionado deletado com sucesso.');
                $this->dispatch('airConditioners-refresh');

            } catch (Exception $e) {
                $this->dispatch('notify-error', $e->getMessage());

            } finally {
                $this->equipmentId = null;
                $this->closeModal();

            }
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $clientes = Client::orderBy('cliente')->get();

        return view('livewire.air-conditioners-manager', [
            'clientes' => $clientes,
        ]);
    }
}
