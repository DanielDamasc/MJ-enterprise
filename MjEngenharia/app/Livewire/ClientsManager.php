<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Str;

class ClientsManager extends Component
{
    public $showModal = false;

    public $nome = '';
    public $telefone = '';
    public $email = '';

    protected $rules = [
        'nome' => 'required',
        'telefone' => 'required',
        'email' => 'nullable|email|unique:clients,email',
    ];

    protected $messages = [
        'nome.required' => 'O campo nome é obrigatório.',
        'telefone.required' => 'O campo telefone é obrigatório.',
        'telefone.unique' => 'O telefone já foi cadastrado.',
        'email.email' => 'Informe um endereço de email válido.',
        'email.unique' => 'O email já foi cadastrado.',

    ];

    public function openModal()
    {
        $this->reset(['nome', 'telefone', 'email']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    // Função para cadastrar um novo cliente.
    public function save()
    {
        $this->validate();

        $telLimpo = preg_replace('/[^0-9]/', '', $this->telefone);

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
            'nome' => $this->nome,
            'telefone' => $this->telefone,
            'email' => $this->email,
            'created_at' => now(),
        ]);

        $this->showModal = false;
        session()->flash('message', 'Cliente cadastrado com sucesso!');

        $this->dispatch('client-created');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.clients-manager');
    }
}
