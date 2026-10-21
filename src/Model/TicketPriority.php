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
}