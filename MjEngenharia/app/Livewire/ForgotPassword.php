<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;

class ForgotPassword extends Component
{
    public $email = '';

    protected $rules = [
        'email' => 'required|email',
    ];

    protected $messages = [
        'email.required' => 'Por favor, informe seu email.',
        'email.email' => 'Informe um endereço de email válido.',
    ];

    // public function sendResetLink()
    // {
    //     $this->validate();

    //     // Tenta enviar o link de redefinição
    //     $status = Password::sendResetLink(['email' => $this->email]);

    //     if ($status === Password::RESET_LINK_SENT) {
    //         // Sucesso: Limpa o campo e exibe mensagem
    //         $this->email = '';
    //         $this->status = __($status); // Traduz a mensagem padrão do Laravel
    //     } else {
    //         // Erro: Adiciona erro ao campo de email
    //         $this->addError('email', __($status));
    //     }
    // }

    #[Layout('layouts.guest')]
    public function render()
    {
        return view('livewire.forgot-password');
    }
}