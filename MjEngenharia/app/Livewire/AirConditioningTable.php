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
        return AirConditioning::query()
            ->with(['client'])
            ->orderByRaw('prox_higienizacao IS NULL ASC, prox_higienizacao ASC');
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
            ->add('modelo')
            ->add('marca')
            ->add('potencia')
            ->add('tipo')
            ->add('prox_higienizacao_formatted', function (AirConditioning $model) {
                // 1. Sem data de próxima higienização
                if (!$model->prox_higienizacao) {
                    return '--/--/----';
                }

                $data = Carbon::parse($model->prox_higienizacao);
                $data_formatada = $data->format('d/m/Y');

                // 2. Se passou a data da próxima higienização
                if ($data->isPast()) {
                    // Badge Vermelho
                    return "<div class='inline-flex items-center px-2.5 py-0.5 rounded-full font-medium bg-red-100 text-red-800'>
                                {$data_formatada}
                            </div>";
                }

                // 3. Caso normal, data futura
                return "<div class='text-gray-800'>{$data_formatada}</div>";
            })
            ->add('prox_higienizacao')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            // Column::make('Id', 'id'),

            // Column::make('Cliente id', 'cliente_id'),

            Column::make('Codigo do AC', 'codigo_ac')
                ->sortable()
                ->searchable(),

            Column::make('Cliente', 'cliente'),

            Column::make('Ambiente', 'ambiente')
                ->sortable()
                ->searchable(),

            Column::make('Potência (BTUs)', 'potencia')
                ->sortable()
                ->searchable(),

            // Column::make('Tipo', 'tipo')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Próxima Higienização', 'prox_higienizacao_formatted', 'prox_higienizacao'),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('prox_higienizacao')
                ->params([
                    'locale'     => 'pt',
                ]),
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
                ->class('text-secondary-600 hover:text-secondary-800 p-1 mr-2 transition-colors')
                ->dispatchTo('air-conditioners-manager', 'open-edit', ['id' => $row->id]),

            Button::add('delete')
                ->slot(Blade::render('<x-heroicon-o-trash class="w-5 h-5" />'))
                ->class('text-red-600 hover:text-red-800 p-1 transition-colors')
                ->dispatchTo('air-conditioners-manager', 'confirm-delete', ['id' => $row->id]),
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
