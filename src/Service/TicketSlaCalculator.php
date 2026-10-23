<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\TicketSlaCalculatorInterface;
use App\Model\Ticket;
use App\Model\TicketPriority;

final class TicketSlaCalculator implements TicketSlaCalculatorInterface
{
    public function getResponseTargetHours(Ticket $ticket): int
    {
        /* Implementación de la lógica del negocio */
        return match ($ticket->getPriority()) {
            TicketPriority::Urgent => 4,
            TicketPriority::High => 8,
            TicketPriority::Normal => 24,
            TicketPriority::Low => 72
        };
    }
}