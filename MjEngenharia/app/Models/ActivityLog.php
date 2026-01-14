<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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

    public function causerName()
    {
        // 1. Get causer Id
        $causerId = $this->causer_id;

        // 2. Get user instance and classname
        $user = User::where('id', $causerId)->first();

        // 3. Verify if the causer is a user
        if ($user) {
            $className = get_class($user);

            // 3. Verify classname and get causer Name
            if ($this->causer_type == $className) {
                $causerName = $user->name;
            }
        } else {
            throw new Exception('O causador da operação não é um usuário.');
        }

        // 4. Return causer Name
        return $causerName;
    }
}
