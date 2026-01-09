<?php

namespace App\Livewire;

use App\Enums\ServiceStatus;
use App\Models\OrderService;
use Blade;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ServicesTable extends PowerGridComponent
{
    public string $tableName = 'servicesTable';

    protected $listeners = ['service-refresh' => '$refresh'];

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
        return OrderService::query()->with(['air_conditioner', 'client', 'user']);
    }

    public function relationSearch(): array
    {
        return [
            'air_conditioner' => [
                'codigo_ac',
            ],
            'client' => [
                'cliente',
            ],
            'user' => [
                'name',
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('ac_id')
            ->add('codigo_ac', function (OrderService $model) {
                return $model->air_conditioner->codigo_ac ?? '-';
            })
            ->add('cliente_id')
            ->add('cliente', function (OrderService $model) {
                return $model->client->cliente ?? '-';
            })
            ->add('executor_id')
            ->add('name', function (OrderService $model) {
                return $model->user->name ?? '-';
            })
            ->add('tipo')
            ->add('data_servico_formatted', fn (OrderService $model) => Carbon::parse($model->data_servico)->format('d/m/Y'))
            ->add('valor')
            ->add('status_formatted', function (OrderService $model) {
                $color = $model->status->color();
                $label = $model->status->label();

                return sprintf(
            '<div class="badge-status bg-%s-100 text-%s-800 px-2 py-1 rounded-md text-sm font-bold inline-block text-center min-w-[80px]">
                        %s
                    </div>',
                    $color,
                    $color,
                    $label
                );
            })
            ->add('status')
            ->add('detalhes')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Código AC', 'codigo_ac'),

            Column::make('Cliente', 'cliente'),

            Column::make('Executor', 'name'),

            // Column::make('Tipo', 'tipo')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Data do Serviço', 'data_servico_formatted', 'data_servico')
                ->sortable(),

            Column::make('Valor', 'valor')
                ->sortable()
                ->searchable(),

            Column::make('Status', 'status_formatted', 'status')
                ->sortable()
                ->searchable(),

            // Column::make('Detalhes', 'detalhes')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Created at', 'created_at_formatted', 'created_at')
            //     ->sortable(),

            // Column::make('Created at', 'created_at')
            //     ->sortable()
            //     ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            // Filter::datepicker('data_servico'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(OrderService $row): array
    {
        return [
            Button::add('done')
                ->slot(Blade::render('<x-heroicon-o-check-circle class="w-5 h-5" />'))
                ->class('text-green-600 hover:text-green-800 p-1 transition-colors')
                ->dispatchTo('services-manager', 'confirm-service-done', ['id' => $row->id]),

            Button::add('delete')
                ->slot(Blade::render('<x-heroicon-o-trash class="w-5 h-5" />'))
                ->class('text-red-600 hover:text-red-800 p-1 transition-colors')
                ->dispatchTo('services-manager', 'confirm-delete', ['id' => $row->id]),
        ];
    }

    public function actionRules($row): array
    {
       return [
           Rule::button('done')
               ->when(fn($row) => $row->status != ServiceStatus::AGENDADO)
               ->hide(),

            Rule::button('delete')
                ->when(fn($row) => $row->status == ServiceStatus::CONCLUIDO)
                ->hide(),
        ];
    }
}
