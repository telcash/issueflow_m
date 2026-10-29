<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contract\TicketCatalogInterface;
use App\Entity\Ticket;

/**
 * Repository Doctrine de Ticket
 */
final class TicketRepository implements TicketCatalogInterface
{
    /* Método que devuelva un array con todas las incidencias */
    public function getTickets(): array
    {
        // TODO: implementación con petición a la BD
        return [];
    }

    /* Método que busca una incidencia en el listado según un
    code proporcionado, y que devuelva null si no existe */
    public function findByCode(string $code): ?Ticket
    {
        // TODO: implementación con petición a la BD
        return null;
    }
}