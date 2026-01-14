<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class LogsTable extends PowerGridComponent
{
    public string $tableName = 'logsTable';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return ActivityLog::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('log_name')
            ->add('description')
            ->add('subject_type')
            ->add('event')
            ->add('subject_id')
            ->add('causer_type')
            ->add('causer_id')
            ->add('causer_name', function(ActivityLog $model) {
                return $model->causerName();
            })
            ->add('properties')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            // Column::make('Id', 'id'),
            // Column::make('Log name', 'log_name')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Description', 'description')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Event', 'event')
                ->sortable()
                ->searchable(),

            Column::make('Subject type', 'subject_type')
                ->sortable()
                ->searchable(),

            Column::make('Subject id', 'subject_id'),
            Column::make('Causer type', 'causer_type')
                ->sortable()
                ->searchable(),

            // Column::make('Causer id', 'causer_id'),
            Column::make('Causer Name', 'causer_name'),
            // Column::make('Properties', 'properties')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Created at', 'created_at_formatted', 'created_at')
            //     ->sortable(),

            Column::make('Make at', 'updated_at')
                ->sortable()
                ->searchable(),

            // Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    // public function actions(ActivityLog $row): array
    // {
    //     return [
    //     ];
    // }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
