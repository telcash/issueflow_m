<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Model\TicketSearchCriteria;
use App\Model\TicketPriority;
use App\Model\TicketStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Contract\TicketCatalogInterface;
use App\Entity\Ticket;
use App\Service\TicketPaginator;

final class ApiTicketController extends AbstractController
{
    public function __construct(
        private readonly TicketCatalogInterface $ticketCatalog,
        private readonly TicketPaginator $ticketPaginator,
    )
    {}
    /**
     * Está petición se hace con el protocolo HTTP, aunque venga de una app móvil
     * Ejemplo de url: miappdeincidencias.com/api/tickets?query=factura&category=1&priority=urgent&page=1
     */
    #[Route('/api/tickets', name: 'app_api_ticket_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        // Reutilizamos el mismo objeto de criterios que ya usa la web
        $criteria = new TicketSearchCriteria();

        /**
         * Procesamos la petición extrayendo del url los distintos parametros.
         * Tengo que validarlos ya que en este caso no están siendo procesados
         * por un formulario
         */

        // Primero extraigo el criterio de búsqueda por código o título, el cual llamamos query
        $criteria->query = trim($request->query->getString('query'));

        // Extraigo el valor de priority
        $priorityValue = trim($request->query->getString('priority'));

        /**
         * Validación de $priorityValue. Si no está vacío, verifico que el
         * valor corresponda con uno de los valores establecidos en el enum TicketPriority
         */
        if ($priorityValue !== '') {
            // tryFrom() permite validar si lo recibido es un valor del enum, si no le asigna el valor de null
            $priority = TicketPriority::tryFrom($priorityValue);

            // Si el valor es null, es decir, la prioridad no corresponde con nada del enum, devuelvo un error
            if ($priority === null) {
                return $this->json(
                    [
                        'error' => [
                            'code' => 'invalid_priority',
                            'message' => 'La prioridad indicada no es válida.'
                        ]
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $criteria->priority = $priority;
        }

        // Extraigo el valor de status
        $statusValue = trim($request->query->getString('status'));

        /**
         * Validación de $statusValue. Si no está vacío, verifico que el
         * valor corresponda con uno de los valores establecidos en el enum TicketStatus
         */
        if ($statusValue !== '') {
            // tryFrom() permite validar si lo recibido es un valor del enum, si no le asigna el valor de null
            $status = TicketStatus::tryFrom($statusValue);

            // Si el valor es null, es decir, la prioridad no corresponde con nada del enum, devuelvo un error
            if ($status === null) {
                return $this->json(
                    [
                        'error' => [
                            'code' => 'invalid_status',
                            'message' => 'El estado indicado no es válido.'
                        ]
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $criteria->status = $status;
        }

        /**
         * Usar el servicio TicketPaginator para obtener la página
         * adecuada
         */
        $ticketPage = $this->ticketPaginator->getTicketPage($criteria, $request->query->get('page'));

        /**
         * Antes de enviar la respuesta al usuario, necesito convertir el objeto WindowPage
         * al formato JSON.
         */
        // El método getItems() devuelve el listado de objetos Ticket
        $tickets = $ticketPage->getItems();

        /**
         * Voy a mapear cada ticket de $tickets de objeto Ticket a JSON
         * La función array_map() mapea todos los elementos de un array dado, aplicando
         * para ello aplica un callback a cada uno de los elementos.
         * El primer argumento es el callback que aplica el mapeo
         * El segundo argumento es el array a mapear
         */
        $items = array_map(
            fn (Ticket $ticket): array => $this->ticketToArray($ticket),
            $tickets
        );

        return $this->json([
            'tickets' => $items
        ]);
    }

    private function ticketToArray(Ticket $ticket): array
    {
        return [
            'code' => $ticket->getCode(),
            'title' => $ticket->getTitle(),
            'priority' => $ticket->getPriority()->value,
            'status' => $ticket->getStatus()->value,
        ];
    }
}