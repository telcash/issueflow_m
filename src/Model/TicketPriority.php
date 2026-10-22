<?php

declare(strict_types=1);

namespace App\Model;

/* 
    Prioridades permitidas por el sistema IssueFlow

    El sistema permita solo cuatro prioridades: 'urgent',
    'high', 'normal', 'low'
*/
enum TicketPriority: string
{
    case Urgent = 'urgent';
    case High = 'high';
    case Normal = 'normal';
    case Low = 'low';

    /*  Un enum también puede tener métodos.
        Implementamos el método getLabel() para
        asignar especificamente el texto que se quiere mostrar al usuario
    */
    public function getLabel(): string
    {
        return match ($this) {
            self::Urgent => 'Urgente',
            self::High => 'Alta',
            self::Normal => 'Normal',
            self::Low => 'Baja'
        };
    }

    /* Método que verifica si la prioridad es urgente
    Se implementa acá porque este es el responsable de prioridades de Ticket */
    public function isUrgent(): bool
    {
        return $this === self::Urgent;
    }
}