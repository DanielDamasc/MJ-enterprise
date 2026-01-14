<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Address extends Model
{
    use LogsActivity;
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

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
