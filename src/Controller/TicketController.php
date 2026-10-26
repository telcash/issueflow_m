<?php

declare(strict_types=1);

namespace App\Controller;

use App\Contract\TicketCatalogInterface;
use App\Contract\TicketSlaCalculatorInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;

final class TicketController extends AbstractController
{
    /* Constructor de TicketController 
    Cuando symfony levanta la aplicación, crea una instancia del controlador.
    Inyecto el servicio TicketCatalog
    Inyecto el servicio TicketSlaCalculator
    */
    public function __construct(
        private readonly TicketCatalogInterface $ticketCatalog,
        private readonly TicketSlaCalculatorInterface $slaCalculator,
        private readonly LoggerInterface $logger
    )
    {}

   /* Crear la ruta /tickets que muestra el listado de todos los tickets
    Por convención se identifica con index a la ruta que muestra un listado
    del recurso (en este caso tickets)
    */
    #[Route('/tickets', name: 'app_ticket_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('ticket/index.html.twig', [
            'tickets' => $this->ticketCatalog->getTickets(),
            'title' => 'Incidencias'
        ]);
    }

    /* Ruta dinámica que muestra la información de un ticket en particular 
    {id} representa una parte variable de la URL
    Ejemplo válido: /tickets/INC-1001
    Por convención se identifica con show a la ruta que muestra un recurso
    en particular (en este caso un ticket)
    */
    #[Route('/tickets/{id}', name: 'app_ticket_show', methods: ['GET'])]
    public function show(string $id): Response
    {
        /* Buscamos el ticket en el listado de tickets, puede suceder
        que el ticket solicitado no exista */
        $ticket = $this->ticketCatalog->find($id);

        /* Si $ticket es NULL la incidencia no existe */
        if ($ticket === NULL) {
            /* Guardo la información en el log para investigaciones posteriores */
            $this->logger->warning('Se solicitó una incidencia no existente', [
                'ticket_id' => $id,
                'route' => 'app_ticket_show',
            ]);

            throw $this->createNotFoundException('No existe la incidencia');
        }

        /* Si $ticket no es NULL, envío a la plantilla la información
        de la incidencia */
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
            'responseTargetHours' => $this->slaCalculator->getResponseTargetHours($ticket)
        ]);
    }
}
