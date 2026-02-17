<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    // Accessor para exibir telefone formatado.
    protected function telefone(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                // 1. Se o telefone estiver vazio no banco, retorna null/vazio
                if (!$value) {
                    return $value;
                }

                // 2. Pega o tamanho do telefone
                $tamanho = strlen($value);

                // 3. Formato Celular (11 dígitos): (XX) X XXXX-XXXX
                if ($tamanho === 11) {
                    return preg_replace('/(\d{2})(\d{1})(\d{4})(\d{4})/', '($1) $2 $3-$4', $value);
                }

                // 4. Se o número tiver um tamanho fora do padrão, retorna do jeito que está no banco
                return $value;
            }
        );
    }

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
