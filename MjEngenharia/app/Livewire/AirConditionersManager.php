<?php

namespace App\Livewire;

use App\Models\AirConditioning;
use App\Models\Client;
use App\Models\User;
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
    // Atributos de Ar-condicionado.
    public $cliente_id = null;
    public $prox_higienizacao = '';
    public $codigo_ac = '';
    public $ambiente = '';
    public $marca = '';
    public $potencia = 0;
    public $tipo = '';

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
            'codigo_ac' => 'required|string|max:50',
            'ambiente' => 'nullable|string|max:100',
            'marca' => 'required|string|max:50',
            'potencia' => 'required|integer|min:1',

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
            'codigo_ac.required' => 'O campo código é obrigatório.',
            'marca.required' => 'O campo marca é obrigatório.',
            'potencia.required' => 'O campo potencia é obrigatório.',
            'tipo.required' => 'O campo tipo é obrigatório.',

            'cep.required' => 'O campo cep é obrigatório.',
            'numero.required' => 'O campo numero é obrigatório.',
        ];
    }

    public function updatedCep($value)
    {
        $cep = preg_replace('/[^0-9]/', '', $value);

        if (strlen($cep) != 8) {
            return ;
        }

        $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");

        if ($response->successful() && !isset($response['erro'])) {
            $dados = $response->json();

            $this->rua = $dados['logradouro'];
            $this->bairro = $dados['bairro'];
            $this->cidade = $dados['localidade'];
            $this->uf = $dados['uf'];

            $this->resetValidation(['rua', 'bairro', 'cidade', 'uf']);
        }
    }

    // public function nextSanitation($value)
    // {
    //     return Carbon::parse($value)->addDays(180);
    // }

    public function closeModal()
    {
        $this->showCreate = $this->showDelete = $this->showEdit = false;
        $this->resetValidation();
    }

    public function openCreate()
    {
        $this->reset([
            'cliente_id',
            'codigo_ac',
            'ambiente',
            'marca',
            'potencia',
            'tipo',
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
            DB::beginTransaction();

            $ac = AirConditioning::create([
                'cliente_id' => $this->cliente_id,
                'codigo_ac' => $this->codigo_ac,
                'ambiente' => $this->ambiente,
                'marca' => $this->marca,
                'potencia' => $this->potencia,
                'tipo' => $this->tipo,
                'prox_higienizacao' => null
            ]);

            $ac->address()->create([
                'cep' => $this->cep,
                'rua' => $this->rua,
                'numero' => $this->numero,
                'bairro' => $this->bairro,
                'complemento' => $this->complemento,
                'cidade' => $this->cidade,
                'uf' => $this->uf
            ]);

            DB::commit();

            session()->flash('message', 'Equipamento cadastrado com sucesso!');
            $this->dispatch('airConditioners-refresh');
        } catch (Exception $e) {
            DB::rollBack();
        } finally {
            $this->showCreate = false;
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

            if ($ac) {
                $ac->delete();
                session()->flash('message', 'Equipamento deletado com sucesso.');
            }
        }

        $this->showDelete = false;
        $this->equipmentId = null;

        $this->dispatch('airConditioners-refresh');
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
            $this->marca = $ac->marca;
            $this->potencia = $ac->potencia;
            $this->tipo = $ac->tipo;

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

        try {
            DB::beginTransaction();

            $ac = AirConditioning::with('address')->find($this->equipmentId);

            if ($ac) {
                $ac->update([
                    'cliente_id' => $this->cliente_id,
                    'codigo_ac' => $this->codigo_ac,
                    'ambiente' => $this->ambiente,
                    'marca' => $this->marca,
                    'potencia' => $this->potencia,
                    'tipo' => $this->tipo,
                ]);

                $ac->address()->updateOrCreate(
            [],
            [
                        'cep' => $this->cep,
                        'rua' => $this->rua,
                        'numero' => $this->numero,
                        'bairro' => $this->bairro,
                        'complemento' => $this->complemento,
                        'cidade' => $this->cidade,
                        'uf' => $this->uf
                    ]);
            }

            DB::commit();

            session()->flash('message', 'Dados atualizados com sucesso!');
            $this->dispatch('airConditioners-refresh');
        } catch (Exception $e) {
            DB::rollBack();
        } finally {
            $this->showEdit = false;
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
