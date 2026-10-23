<?php

declare(strict_types=1);

namespace App\Contract;

use App\Model\Ticket;

interface TicketSlaCalculatorInterface
{
    public function getResponseTargetHours(Ticket $ticket): int;
}