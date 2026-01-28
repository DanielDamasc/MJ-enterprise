<?php

namespace App\Livewire;

use App\Models\Client;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Str;

class ClientsManager extends Component
{
    public $cliente = '';
    public $contato = '';
    public $telefone = '';
    public $email = '';
    public $tipo = '';

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
            ]
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

    protected function normalizeTelefone($tel)
    {
        return preg_replace('/[^0-9]/', '', $tel);
    }

    public function closeModal()
    {
        $this->showCreate = $this->showDelete = $this->showEdit = false;
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
        $this->reset(['cliente', 'contato', 'telefone', 'email', 'tipo', 'clientId']);
        $this->resetValidation();
        $this->showCreate = true;
    }

    // Função para cadastrar um novo cliente.
    public function save()
    {
        $this->validate();

        // --- VALIDAÇÕES ESPECIAIS PARA TELEFONE ---
        $telLimpo = $this->normalizeTelefone($this->telefone);

        $telExists = Client::where('telefone', $telLimpo)->exists();

        if ($telExists) {
            $this->addError('telefone', 'Este telefone já foi cadastrado.');
            return ;
        }

        if (Str::of($telLimpo)->length() !== 11) {
            $this->addError('telefone', 'O telefone deve ter 11 dígitos.');
            return ;
        }

        Client::create([
            'cliente' => $this->cliente,
            'contato' => $this->contato,
            'telefone' => $telLimpo,
            'email' => $this->email,
            'tipo' => $this->tipo,
        ]);

        $this->closeModal();
        $this->dispatch('notify-success', 'Cliente cadastrado com sucesso!');

        $this->dispatch('client-refresh');
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

            if ($client->servicos()->withTrashed()->exists()) {
                $this->dispatch('notify-error', 'Não se pode deletar um cliente com serviço vinculado.');
                $this->closeModal();
                return ;
            }

            if ($client) {
                $client->delete();
                $this->dispatch('notify-success', 'Cliente deletado com sucesso.');
            }
        }

        $this->closeModal();
        $this->clientId = null;

        $this->dispatch('client-refresh');
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
        }
    }

    public function edit()
    {
        $this->validate();

        // --- VALIDAÇÕES ESPECIAIS PARA TELEFONE ---
        $telLimpo = $this->normalizeTelefone($this->telefone);

        // Verifica se existe alguém com o telefone que não seja o cliente atual.
        $telExists = Client::where('telefone', $telLimpo)
                    ->where('id', '!=', $this->clientId)
                    ->exists();

        if ($telExists) {
            $this->addError('telefone', 'Este telefone já foi cadastrado.');
            return ;
        }

        if (Str::of($telLimpo)->length() !== 11) {
            $this->addError('telefone', 'O telefone deve ter 11 dígitos.');
            return ;
        }

        $client = Client::find($this->clientId);

        if ($client) {
            $client->update([
                'cliente' => $this->cliente,
                'contato' => $this->contato,
                'telefone' => $telLimpo,
                'email' => $this->email,
                'tipo' => $this->tipo,
            ]);
        }

        $this->closeModal();
        $this->dispatch('notify-success', 'Dados atualizados com sucesso!');

        $this->dispatch('client-refresh');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.clients-manager');
    }
}
