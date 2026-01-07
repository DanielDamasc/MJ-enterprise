<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AirConditioning extends Model
{
    protected $table = 'air_conditioners';

    protected $fillable = [
        'cliente_id', // chave estrangeira
        'executor_id', // chave estrangeira

        'codigo_ac', // identificador do AC (não é chave primária)

        'ambiente', // ambiente em que está instalado o AC

        'ultima_higienizacao', // data
        'prox_higienizacao', // data da próxima higienização

        'marca', // marca do AC
        'potencia', // potencia do AC
        'tipo', // tipo do AC

        'valor', // valor cobrado
        'limpou_condensadora' // bool
    ];

    protected static function booted()
    {
        static::deleting(function ($ac) {
            $ac->address()->delete();
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'cliente_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'executor_id');
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }
}
