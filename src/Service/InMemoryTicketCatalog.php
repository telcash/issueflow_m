<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\TicketCatalogInterface;
use App\Model\Ticket;
use App\Model\TicketPriority;
use App\Model\TicketStatus;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

/* Catálogo temporal de incidencias en memoria 
    Implementa la interfaz TicketCatalogInterface
*/
/* Al usar AsAlias, por defecto, el contenedor de servicios de Symfony inyectará esta clase
    cuando se solicite una implementación de TicketCatalogInterface */
#[AsAlias(TicketCatalogInterface::class)]
final class InMemoryTicketCatalog implements TicketCatalogInterface
{
    // Atributo que almacena el listado de las incidencias
    private array $tickets;

    public function __construct()
    {
        $this->tickets = [
            new Ticket(
                id: 'INC-1001',
                title: 'No puedo acceder',
                priority: TicketPriority::Urgent,
                status: TicketStatus::Open
            ),
            new Ticket(
                id: 'INC-1002',
                title: 'Error en factura',
                priority: TicketPriority::High,
                status: TicketStatus::InProgress
            ),
            new Ticket(
                id: 'INC-1003',
                title: 'Actualizar datos',
                priority: TicketPriority::Normal,
                status: TicketStatus::Closed
            ),
            new Ticket(
                id: 'INC-1004',
                title: 'El ordenador está en llamas',
                priority: TicketPriority::Urgent,
                status: TicketStatus::InProgress
            ),
            new Ticket(
                id: 'INC-4532',
                title: 'Olvidé mi contraseña',
                priority: TicketPriority::Low,
                status: TicketStatus::Open
            )
        ];
    }

    /* Método que devuelva el listado de todos los tickets */
    public function getTickets(): array
    {
        return $this->tickets;
    }

    /* Metodo find que encuentra un ticket en el listado, dado su id 
    ?Ticket quiere decir que el método puede devolver o un Ticket o null
    */
    public function find(string $id): ?Ticket
    {
        /* array_find(), disponible en PHP 8.4+ devuelve el primer elemento
        del array que cumple con la condición indicada por la función callback.
        Si no encuentra ningún elemento que cumpla la condición devuelve null */
        return array_find(
            $this->tickets,
            fn (Ticket $ticket): bool => $ticket->getId() === $id
        );
    }
}