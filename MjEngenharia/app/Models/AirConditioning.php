<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AirConditioning extends Model
{
    protected $table = 'air_conditioners';

    protected $fillable = [
        'cliente_id', // chave estrangeira

        'codigo_ac', // identificador do AC (não é chave primária)

        'ambiente', // ambiente em que está instalado o AC

        'instalacao', // data de instalação
        'prox_higienizacao', // data da próxima higienização

        'marca', // marca do AC
        'potencia', // potencia do AC
        'tipo', // tipo do AC

        'valor', // valor cobrado
        'valor_com_material' // S/N se o valor cobrado inclui o material
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'cliente_id');
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }
}
