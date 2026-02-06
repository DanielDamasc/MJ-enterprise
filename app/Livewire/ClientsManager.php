<?php

namespace App\Livewire;

use App\Models\Client;
use App\Services\ClientService;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Str;

class ClientsManager extends Component
{
    protected ClientService $clientService;

    public function boot(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    // Atributos de Cliente.
    public $cliente = '';
    public $contato = '';
    public $telefone = '';
    public $email = '';
    public $tipo = '';

    // Atributos de Endereço.
    public $cep = '';
    public $rua = '';
    public $numero = '';
    public $bairro = '';
    public $complemento = '';
    public $cidade = '';
    public $uf = '';

    // Outros Atributos Auxiliares.
    public $showAddress = false;
    public $showDetails = false;
    public $showCreate = false;
    public $showDelete = false;
    public $showEdit = false;
    public $clientId = null;

    public $equipmentList = [];

    protected function rules()
    {
        return [
            'cliente' => 'required',
            'contato' => 'required',
            'telefone' => 'required',
            'email' => [
                'nullable',
                'email',
                Rule::unique('clients', 'email')->ignore($this->clientId),
            ],
            'tipo' => [
                'required',
                'string',
                Rule::in(['residencial', 'comercial']),
            ],

            'cep' => 'nullable|string|max:9',
            'rua' => [
                'nullable','string','max:255',
                Rule::requiredIf(fn() =>
                    $this->cep != ''
                ),
            ],
            'numero' => [
                'nullable','string','max:20',
                Rule::requiredIf(fn() =>
                    $this->cep != ''
                ),
            ],
            'bairro' => [
                'nullable','string','max:100',
                Rule::requiredIf(fn() =>
                    $this->cep != ''
                ),
            ],
            'complemento' => [
                'nullable','string','max:150',
            ],
            'cidade' => [
                'nullable','string','max:100',
                Rule::requiredIf(fn() =>
                    $this->cep != ''
                ),
            ],
            'uf' => [
                'nullable','string','size:2',
                Rule::requiredIf(fn() =>
                    $this->cep != ''
                ),
            ],
        ];
    }

    protected $messages = [
        'cliente.required' => 'O campo cliente é obrigatório.',
        'contato.required' => 'Informe o nome da pessoa de contato.',
        'telefone.required' => 'O campo telefone é obrigatório.',
        'telefone.unique' => 'O telefone já foi cadastrado.',
        'email.email' => 'Informe um endereço de email válido.',
        'email.unique' => 'O email já foi cadastrado.',
        'tipo.required' => 'O campo tipo é obrigatório.',
    ];

    public function updatedCep($value)
    {
        $cep = preg_replace('/[^0-9]/', '', $value);

        if (strlen($cep) != 8) {
            return ;
        }

        $response = Http::withOptions([
            'verify' => true,
        ])
        ->withUserAgent('MjEngenharia')
        ->timeout(10)
        ->get("https://viacep.com.br/ws/{$cep}/json/");

        if ($response->successful() && !isset($response['erro'])) {
            $dados = $response->json();

            $this->rua = $dados['logradouro'];
            $this->bairro = $dados['bairro'];
            $this->cidade = $dados['localidade'];
            $this->uf = $dados['uf'];

            $this->resetValidation(['rua', 'bairro', 'cidade', 'uf']);
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

    public function closeModal()
    {
        $this->showCreate = $this->showDelete = $this->showEdit = $this->showAddress = false;
        $this->resetValidation();
    }

    public function closeDetails()
    {
        $this->showDetails = false;
        $this->equipmentList = [];
    }

    #[On('open-details')]
    public function openDetails($id)
    {
        $this->clientId = $id;

        $client = Client::with('airConditioners')->find($this->clientId);
        if ($client) {
            $this->equipmentList = $client->airConditioners;
        }

        $this->showDetails = true;
    }

    public function openCreate()
    {
        $this->reset([
            'cliente',
            'contato',
            'telefone',
            'email',
            'tipo',
            'clientId',
        ]);
        $this->clearAddress();
        $this->resetValidation();
        $this->showCreate = true;
    }

    public function save()
    {
        $this->validate();

        try {
            $this->clientService->create([
                'cliente' => $this->cliente,
                'contato' => $this->contato,
                'telefone' => $this->telefone,
                'email' => $this->email,
                'tipo' => $this->tipo,
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
            $this->dispatch('notify-success', 'Cliente cadastrado com sucesso!');
            $this->dispatch('client-refresh');

        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());
        }
    }

    #[On('open-edit')]
    public function openEdit($id)
    {
        $this->clientId = $id;
        $this->showEdit = true;

        if ($this->clientId) {
            $client = Client::find($this->clientId);
            $this->cliente = $client->cliente;
            $this->contato = $client->contato;
            $this->telefone = $client->telefone;
            $this->email = $client->email ?? ''; // E-mail pode ser null.
            $this->tipo = $client->tipo;

            // Buscando dados do endereço.
            if ($client->address) {
                $this->showAddress = true; // Abre o componente de endereço.
                $this->cep = $client->address->cep;
                $this->rua = $client->address->rua;
                $this->numero = $client->address->numero;
                $this->bairro = $client->address->bairro;
                $this->complemento = $client->address->complemento ?? '';
                $this->cidade = $client->address->cidade;
                $this->uf = $client->address->uf;
            } else {
                $this->showAddress = false;
                // Limpa caso tenha aberto um com endereço antes.
                $this->clearAddress();
            }
        }
    }

    public function edit()
    {
        $this->validate();

        $client = Client::find($this->clientId);

        if ($client) {
            try {
                $this->clientService->update($client, [
                    'cliente' => $this->cliente,
                    'contato' => $this->contato,
                    'telefone' => $this->telefone,
                    'email' => $this->email,
                    'tipo' => $this->tipo,
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
                $this->dispatch('client-refresh');

            } catch(Exception $e) {
                $this->dispatch('notify-error', $e->getMessage());

            }
        }
    }

    #[On('confirm-delete')]
    public function confirmDelete($id)
    {
        $this->clientId = $id;
        $this->showDelete = true;
    }

    public function delete()
    {
        if ($this->clientId) {
            $client = Client::find($this->clientId);

            try {
                $this->clientService->delete($client);
                $this->dispatch('notify-success', 'Cliente deletado com sucesso.');
                $this->dispatch('client-refresh');

            } catch (Exception $e) {
                $this->dispatch('notify-error', $e->getMessage());

            } finally {
                $this->clientId = null;
                $this->closeModal();

            }
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.clients-manager');
    }
}
