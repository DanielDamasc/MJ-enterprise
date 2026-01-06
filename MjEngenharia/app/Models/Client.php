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

    protected static function booted()
    {
        static::deleting(function ($client) {
            $client->air_conditioners->each(function ($ac) {
                $ac->delete();
            });
        });
    }

    public function air_conditioners()
    {
        return $this->hasMany(AirConditioning::class, 'cliente_id');
    }
}
