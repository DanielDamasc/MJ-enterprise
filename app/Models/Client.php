<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Client extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'clients';

    protected $fillable = [
        'cliente',
        'contato',
        'telefone',
        'email',
        'tipo', // residencial ou comercial
        'ultima_notificacao',
        'qtd_notificacoes'
    ];

    // Converte automaticamente o dado que vem do banco para o datetime do Carbon.
    protected $casts = [
        'ultima_notificacao' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    protected static function booted()
    {
        static::deleting(function ($client) {
            $client->airConditioners->each(function ($ac) {
                $ac->delete();
            });
        });
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function airConditioners()
    {
        return $this->hasMany(AirConditioning::class, 'cliente_id');
    }

    public function servicos()
    {
        return $this->hasMany(OrderService::class, 'cliente_id');
    }
}
