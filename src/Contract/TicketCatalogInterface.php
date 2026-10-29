<?php

declare(strict_types=1);

namespace App\Contract;

use App\Entity\Ticket;

/*
    Contrato que define que necesita mi aplicación de cualquier catálogo
    de tickets.
    La interfaz describe que operaciones deben existir, pero no como se llevan
    a cabo.
*/
interface TicketCatalogInterface
{
    /* Método que devuelva un array con todas las incidencias */
    public function getTickets(): array;

    /* Método que busca una incidencia en el listado según un
    code proporcionado, y que devuelva null si no existe */
    public function findByCode(string $code): ?Ticket;
}