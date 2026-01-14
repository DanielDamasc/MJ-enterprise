<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class LogsManager extends Component
{
    public $showModal = false;

    public $createdData = '';
    public $updatedData = '';
    public $deletedData = '';
    public $model = '';

    public function closeModal()
    {
        $this->showModal = false;
    }

    #[On('show')]
    public function show($id)
    {
        // 1. reset variables
        $this->reset([
            'createdData',
            'updatedData',
            'deletedData',
            'model',
        ]);

        // 2. get the log register
        $log = ActivityLog::find($id);

        // 3. verify
        if (!$log || empty($log->properties)) {
            $this->dispatch('notify-error', 'Log sem propriedades ou não encontrado!');
            $this->closeModal();
            return ;
        }

        // 4. get the model
        $this->model = $log->subject_type;

        // if create operation
        if (array_key_exists('old', $log->properties) == false) {
            $this->createdData = $log->properties["attributes"];
        }

        // if delete operation
        if (array_key_exists('attributes', $log->properties) == false) {
            $this->deletedData = $log->properties["old"];
        }

        // if update operation
        if (array_key_exists('old', $log->properties) && array_key_exists('attributes', $log->properties)) {
            $oldData = $log->properties["old"];
            $newData = $log->properties["attributes"];

            $newAttr = array_diff($newData, $oldData);
            $oldAttr = array_diff($oldData, $newData);

            $this->updatedData = [
                'old' => $oldAttr,
                'new' => $newAttr,
            ];
        }

        // 5. show the modal
        $this->showModal = true;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.logs-manager');
    }
}
