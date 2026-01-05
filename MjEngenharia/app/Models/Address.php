<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';
    
    protected $fillable = [
        'cep',
        'rua',
        'numero',
        'bairro',
        'complemento',
        'cidade',
        'uf',

        // Atributos para polimorfia.
        'addressable_id',
        'addressable_type'
    ];

    public function addressable()
    {
        return $this->morphTo();
    }
}
