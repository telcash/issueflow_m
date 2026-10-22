<?php

declare(strict_types=1);

namespace App\Model;

/* 
    Estados permitidos por el sistema IssueFlow

    El sistema permita solo tres estados:
    'open', 'in_progress', 'closed'
*/
enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Closed = 'closed';

    /*  Un enum también puede tener métodos.
        Implementamos el método getLabel() para
        asignar especificamente el texto que se quiere mostrar al usuario
    */
    public function getLabel(): string
    {
        return match ($this) {
            self::Open => 'Abierto',
            self::InProgress => 'En Progreso',
            self::Closed => 'Cerrado'
        };
    }

    /* Método que devuelve true si una incidencia está cerrada */
    public function isClosed(): bool
    {
        return $this === self::Closed;
    }
}