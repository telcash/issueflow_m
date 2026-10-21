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
}