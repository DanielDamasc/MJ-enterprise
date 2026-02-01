<?php

namespace App\Enums;

enum ServiceStatus: string
{
    case AGENDADO = 'agendado';
    case CONCLUIDO = 'concluido';
    case CANCELADO = 'cancelado';

    public function label()
    {
        return match($this) {
            self::AGENDADO => 'Agendado',
            self::CONCLUIDO => 'Concluido',
            self::CANCELADO => 'Cancelado',
        };
    }

    public function color()
    {
        return match($this) {
            self::AGENDADO => 'blue',
            self::CONCLUIDO => 'green',
            self::CANCELADO => 'red',
        };
    }
}
