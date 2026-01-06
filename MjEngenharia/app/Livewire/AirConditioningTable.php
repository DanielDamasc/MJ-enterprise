<?php

namespace App\Livewire;

use App\Models\AirConditioning;
use Blade;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class AirConditioningTable extends PowerGridComponent
{
    public string $tableName = 'airConditioningTable';

    protected $listeners = ['airConditioners-refresh' => '$refresh'];

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
        return AirConditioning::query()->with('client');
    }

    public function relationSearch(): array
    {
        return [
            'client' => [
                'cliente'
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('cliente_id')
            ->add('cliente', function (AirConditioning $model) {
                return $model->client->cliente ?? '-';
            })
            ->add('codigo_ac')
            ->add('ambiente')
            ->add('instalacao_formatted', fn (AirConditioning $model) => Carbon::parse($model->instalacao)->format('d/m/Y'))
            ->add('prox_higienizacao_formatted', fn (AirConditioning $model) => Carbon::parse($model->prox_higienizacao)->format('d/m/Y'))
            ->add('marca')
            ->add('potencia')
            ->add('tipo')
            ->add('valor')
            ->add('valor_com_material')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            // Column::make('Id', 'id'),

            // Column::make('Cliente id', 'cliente_id'),

            Column::make('Cliente', 'cliente'),

            Column::make('Codigo do AC', 'codigo_ac')
                ->sortable()
                ->searchable(),

            Column::make('Ambiente', 'ambiente')
                ->sortable()
                ->searchable(),

            // Column::make('Instalacao', 'instalacao_formatted', 'instalacao')
            //     ->sortable(),

            Column::make('Próxima Higienização', 'prox_higienizacao_formatted', 'prox_higienizacao')
                ->sortable(),

            Column::make('Potência (BTUs)', 'potencia')
                ->sortable()
                ->searchable(),

            // Column::make('Tipo', 'tipo')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Valor', 'valor')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Valor com material', 'valor_com_material')
            //     ->sortable()
            //     ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            // Filter::datepicker('instalacao'),
            // Filter::datepicker('prox_higienizacao'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(AirConditioning $row): array
    {
        return [
            Button::add('edit')
                ->slot(Blade::render('<x-heroicon-o-pencil-square class="w-5 h-5" />'))
                ->class('text-secondary-600 hover:text-secondary-800 p-1 mr-2 transition-colors'),
                // ->dispatchTo('clients-manager', 'open-edit', ['id' => $row->id]),

            Button::add('delete')
                ->slot(Blade::render('<x-heroicon-o-trash class="w-5 h-5" />'))
                ->class('text-red-600 hover:text-red-800 p-1 transition-colors'),
                // ->dispatchTo('clients-manager', 'confirm-delete', ['id' => $row->id]),
        ];
    }

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
