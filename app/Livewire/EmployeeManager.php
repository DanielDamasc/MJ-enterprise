<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\EmployeeService;
use Exception;
use Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Str;

class EmployeeManager extends Component
{
    protected EmployeeService $employeeService;

    public function boot(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public $name = '';
    public $email = '';
    public $showCreate = false;
    public $showDelete = false;
    public $showEdit = false;
    public $userId = null;

    protected function rules()
    {
        return [
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
        ];
    }

    protected $messages = [
        'name.required' => 'O campo nome é obrigatório.',
        'email.required' => 'O campo email é obrigatório.',
        'email.email' => 'Informe um endereço de email válido.',
        'email.unique' => 'O email já foi cadastrado.',
    ];

    public function closeModal()
    {
        $this->showCreate = $this->showDelete = $this->showEdit = false;
        $this->resetValidation();
    }

    public function openCreate()
    {
        $this->reset(['name', 'email', 'userId']);
        $this->resetValidation();
        $this->showCreate = true;
    }

    public function save()
    {
        $this->validate();

        try {
            $this->employeeService->create([
                'name' => $this->name,
                'email' => $this->email,
            ]);

            $this->closeModal();
            $this->dispatch('notify-success', 'Executor cadastrado com sucesso!');
            $this->dispatch('employee-refresh');

        } catch (Exception $e) {
            $this->dispatch('notify-error', $e->getMessage());

        }
    }

    #[On('open-edit')]
    public function openEdit($id)
    {
        $this->userId = $id;
        $this->showEdit = true;

        if ($this->userId) {
            $user = User::find($this->userId);
            $this->name = $user->name;
            $this->email = $user->email;
        }
    }

    public function edit()
    {
        $this->validate();

        $user = User::find($this->userId);

        if ($user) {
            try {
                $this->employeeService->update($user,[
                    'name' => $this->name,
                    'email' => $this->email,
                ]);

                $this->closeModal();
                $this->dispatch('notify-success', 'Dados atualizados com sucesso!');
                $this->dispatch('employee-refresh');

            } catch (Exception $e) {
                $this->dispatch('notify-error', $e->getMessage());

            }
        }
    }

    #[On('confirm-delete')]
    public function confirmDelete($id)
    {
        $this->userId = $id;
        $this->showDelete = true;
    }

    public function delete()
    {
        if ($this->userId) {
            $user = User::find($this->userId);

            try {
                $this->employeeService->delete($user);
                $this->dispatch('notify-success', 'Executor deletado com sucesso.');
                $this->dispatch('employee-refresh');

            } catch (Exception $e) {
                $this->dispatch('notify-error', $e->getMessage());

            } finally {
                $this->userId = null;
                $this->closeModal();

            }
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.employee-manager');
    }
}
