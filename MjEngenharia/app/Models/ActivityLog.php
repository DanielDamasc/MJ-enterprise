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

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    public function getCauserLabelAttribute()
    {
        return $this->causer?->name ?? 'Usuário Excluído';
    }
}
