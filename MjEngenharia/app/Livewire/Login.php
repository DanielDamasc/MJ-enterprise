<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    // Regras de validação
    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8',
    ];

    // Mensagens em Português
    protected $messages = [
        'email.required' => 'O campo email é obrigatório.',
        'email.email' => 'Por favor, insira um email válido.',
        'password.required' => 'A senha é obrigatória.',
        'password.min' => 'A senha deve ter no mínimo 8 dígitos.'
    ];

    public function login()
    {
        $this->validate();

        // Tenta autenticar
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            // Redireciona para o dashboard ou rota pretendida
            return redirect()->intended('/');
        }

        // Se falhar, adiciona erro ao campo email
        $this->addError('email', 'Essas credenciais não correspondem aos nossos registros.');
    }

    #[Layout('layouts.guest')]
    public function render()
    {
        // Define o layout se você não estiver usando o padrão
        return view('livewire.login');
    }
}
