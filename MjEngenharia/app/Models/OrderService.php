<?php

namespace App\Models;

use App\Enums\ServiceStatus;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OrderService extends Model
{
    use SoftDeletes, LogsActivity;
    protected $table = 'order_services';

    protected $fillable = [
        // Chaves Estrangeiras
        'cliente_id',
        'executor_id',

        // Atributos
        'tipo',
        'data_servico',
        'total', // Valor total, persistido como soma dos valores unitários
        'status', // enum

        // Campo Json para atributos específicos
        'detalhes' // limpou_condensadora
    ];

    public $casts = [
        'status' => ServiceStatus::class,
        'detalhes' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    // 1. Método para concluir service
    public function concluir()
    {
        if (Carbon::parse($this->data_servico)->startOfDay()->isFuture()) {
            throw new Exception('Não é possível finalizar um serviço agendado para uma data futura.');
        }

        if ($this->status !== ServiceStatus::AGENDADO) {
            throw new Exception('Apenas serviços agendados podem ser concluídos.');
        }

        DB::transaction(function () {

            // Atualiza o status do serviço.
            $this->update([
                'status' => ServiceStatus::CONCLUIDO->value,
            ]);

            // Atualiza a data da próxima higienização.
            if ($this->tipo == 'higienizacao') {
                $proxData = $this->proximaHigienizacao($this->data_servico);

                $this->airConditioners()->update([
                    'prox_higienizacao' => $proxData
                ]);
            }

            // Atualiza atributos de notificação de cliente.
            $this->client->update([
                'ultima_notificacao' => null,
                'qtd_notificacoes' => 0,
            ]);
        });
    }

    // 2. Método para calcular a próxima higienização
    public function proximaHigienizacao($dataServico)
    {
        if (!$this->client) {
            throw new Exception('Não há Cliente vinculado à Ordem de Serviço.');
        }

        if ($this->client->tipo == 'comercial') {
            return Carbon::parse($dataServico)->addMonths(6);
        }
        if ($this->client->tipo == 'residencial') {
            return Carbon::parse($dataServico)->addMonths(12);
        }
        throw new Exception('Erro ao calcular a data da próxima higienização.');
    }

    public function airConditioners()
    {
        return $this->belongsToMany(AirConditioning::class,
            'order_service_items',
            'order_service_id',
            'air_conditioning_id',
            'id',
            'id')
            ->withPivot('valor');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'cliente_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'executor_id');
    }
}
