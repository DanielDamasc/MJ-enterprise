<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'cliente',
        'contato',
        'telefone',
        'email'
    ];

    public function air_conditioners()
    {
        return $this->hasMany(AirConditioning::class);
    }
}
