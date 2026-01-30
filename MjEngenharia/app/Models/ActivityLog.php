<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $table = 'activity_log';

    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'event',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
    ];

    public $casts = [
        'properties' => 'array',
    ];

    protected static $eventLabels = [
        'created' => 'Criou',
        'updated' => 'Atualizou',
        'deleted' => 'Excluiu',
        'sent' => 'Enviou',
    ];

    protected static $subjectTypeLabels = [
        'App\Models\User' => 'Usuário',
        'App\Models\Client' => 'Cliente',
        'App\Models\AirConditioning' => 'Ar-condicionado',
        'App\Models\Address' => 'Endereço',
        'App\Models\OrderService' => 'Ordem de Serviço'
    ];

    public function getEventLabelAttribute()
    {
        return self::$eventLabels[$this->event] ?? ucfirst($this->event);
    }

    public function getSubjectTypeLabelAttribute()
    {
        return self::$subjectTypeLabels[$this->subject_type] ?? ucfirst($this->subject_type);
    }

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    public function getCauserNameAttribute()
    {
        // 1. Se não tem o ID, foi o sistema
        if (is_null($this->causer_id)) {
            return 'Sistema Automático';
        }

        // 2. Tenta pegar o nome, se não consegue, significa usuário deletado.
        return $this->causer?->name ?? 'Usuário Excluído';
    }

    public function getCauserRoleAttribute()
    {
        if (is_null($this->causer_id)) {
            return 'Automático';
        }

        if ($this->causer_type == 'App\Models\User' && $this->causer) {

            // Regra: o causer_type será exibido de acordo com o role do usuário.
            if ($this->causer->hasRole('adm')) {
                return 'Administrador';
            }

            if ($this->causer->hasRole('executor')) {
                return 'Executor';
            }

            // Fallback de segurança.
            return 'Usuário';
        }

        return '-';
    }
}
