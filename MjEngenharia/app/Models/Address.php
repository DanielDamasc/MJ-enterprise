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

    // Retorna a string para abrir o google maps com o endereço completo.
    public function getEnderecoAttribute()
    {
        return "{$this->rua}, {$this->numero} - {$this->bairro}, {$this->cep}";
    }

    public function addressable()
    {
        return $this->morphTo();
    }
}
