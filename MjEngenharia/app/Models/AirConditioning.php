<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AirConditioning extends Model
{
    protected $table = 'air_conditioners';

    protected $fillable = [
        // chave estrangeira
        'cliente_id',

        // dados básicos do AC
        'codigo_ac',
        'ambiente',
        'marca',
        'potencia',
        'tipo',

        // data da próxima higienização
        'prox_higienizacao',
    ];

    // protected static function booted()
    // {
    //     static::deleting(function ($ac) {
    //         $ac->address()->delete();
    //     });
    // }

    public function client()
    {
        return $this->belongsTo(Client::class, 'cliente_id');
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function servicos()
    {
        return $this->hasMany(OrderService::class, 'ac_id');
    }
}
