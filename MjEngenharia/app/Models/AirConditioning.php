<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AirConditioning extends Model
{
    use HasFactory, LogsActivity;
    protected $table = 'air_conditioners';

    protected $fillable = [
        // chave estrangeira
        'cliente_id',

        // dados básicos do AC
        'codigo_ac',
        'ambiente',
        'modelo',
        'marca',
        'potencia',
        'tipo',

        // data da próxima higienização
        'prox_higienizacao',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    public static function gerarCodigo($clienteId)
    {
        // 1. Pega o útlimo AC criado.
        $ultimoAC = self::where('cliente_id', $clienteId)
            ->latest('id')
            ->first();

        // 2. Caso nenhum tenha sido criado, quer dizer que é o primeiro.
        if (!$ultimoAC) {
            return 'AC1';
        }

        // 3. Tenta extrair apenas o número do código do último AC criado (ex: AC8 => 8).
        $ultimoCod = (int) filter_var($ultimoAC->codigo_ac, FILTER_SANITIZE_NUMBER_INT);

        // 4. Fallback com a contagem para caso a etapa anterior falhe.
        if ($ultimoCod <= 0) {
            $contagem = self::where('cliente_id', $clienteId)->count();
            return 'AC' . ($contagem + 1);
        }

        // 4. Próximo número do código.
        $proximoCod = $ultimoCod + 1;

        // 5. Retorna o código.
        return 'AC' . $proximoCod;
    }

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
        return $this->belongsToMany(OrderService::class,
            'order_service_items',
            'air_conditioning_id',
            'order_service_id',
            'id',
            'id')
            ->withPivot('valor');
    }
}
