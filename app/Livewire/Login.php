<?php

namespace App\Livewire;

use App\Models\User;
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

        // Tenta logar.
        $loggedIn = User::withoutEvents(function () {
            return Auth::attempt(
                ['email' => $this->email, 'password' => $this->password],
                $this->remember
            );
        });

        // Se funcionou, autentica, atribui role, e redireciona.
        if ($loggedIn) {
            session()->regenerate();

            $user = Auth::user();

            if ($user->hasRole('executor')) {
                return redirect()->to('/servicos-executor');
            }

            if ($user->hasRole('assistente')) {
                return redirect()->to('/clientes');
            }

            return redirect()->to('/');
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
