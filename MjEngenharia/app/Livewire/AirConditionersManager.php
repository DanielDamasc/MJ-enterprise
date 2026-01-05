<?php

namespace App\Livewire;

use App\Models\Client;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AirConditionersManager extends Component
{
    // Atributos de Ar-condicionado.
    public $cliente_id = null;
    public $codigo_ac = '';
    public $ambiente = '';
    public $instalacao = ''; // date
    public $prox_higienizacao = ''; // date
    public $marca = '';
    public $potencia = 0; // integer
    public $tipo = '';
    public $valor = 0; // decimal
    public $valor_com_material = false; // bool

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

    // protected function rules()
    // {
    //     return [
    //         'cliente_id' => 'required|integer|exists:clients,id',
    //         'codigo_ac' => 'nullable',
    //         'ambiente' => 'nullable',
    //         'instalacao' => 'required|date',
    //         'prox_higienizacao' => 'date',
    //         'marca' => 'required',
    //         'potencia' => 'required|integer',
    //         'tipo' => 'required',
    //         'valor' => 'required|numeric',
    //         'valor_com_material' => 'boolean:strict'
    //     ];
    // }

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

    public function openCreate()
    {
        $this->reset([
            'codigo_ac',
            'ambiente',
            'instalacao',
            'prox_higienizacao',
            'marca',
            'potencia',
            'tipo',
            'valor',
            'valor_com_material'
        ]);
        $this->resetValidation();

        $this->showCreate = true;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $clientes = Client::orderBy('cliente')->get();

        return view('livewire.air-conditioners-manager', [
            'clientes' => $clientes
        ]);
    }
}
