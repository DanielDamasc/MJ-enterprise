<?php

namespace App\Models;

use App\Enums\ServiceStatus;
use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    protected $table = 'order_services';

    protected $fillable = [
        // Chaves Estrangeiras
        'ac_id',
        'cliente_id',
        'executor_id',

        // Atributos
        'tipo',
        'data_servico',
        'valor',
        'status', // enum

        // Campo Json para atributos específicos
        'detalhes' // limpou_condensadora
    ];

    public $casts = [
        'status' => ServiceStatus::class,
        'detalhes' => 'array',
    ];

    public function air_conditioner()
    {
        return $this->belongsTo(AirConditioning::class, 'ac_id');
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
