<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class TicketController extends AbstractController
{
    /* Mock data de tickets: Data simulada de prueba. La data real
    se obtendrá luego de una petición a la base de datos. 
    La data viene en un array constante de clase
    */
    private const TICKETS = [
        [
            'id' => 101,
            'title' => 'No puedo acceder',
            'priority' => 'urgent',
            'status' => 'open',
        ],
        [
            'id' => 102,
            'title' => 'Error en factura',
            'priority' => 'high',
            'status' => 'in_progress',
        ],
        [
            'id' => 103,
            'title' => 'Actualizar datos',
            'priority' => 'normal',
            'status' => 'closed',
        ],
        [
            'id' => 104,
            'title' => 'El ordenador está en llamas',
            'priority' => 'urgent',
            'status' => 'in_progress',
        ],
    ];

    /* Crear la ruta /tickets que muestra el listado de todos los tickets
    Por convención se identifica con index a la ruta que muestra un listado
    del recurso (en este caso tickets)
    */
    #[Route('/tickets', name: 'app_ticket_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('ticket/index.html.twig', [
            'tickets' => self::TICKETS,
            'title' => 'Incidencias'
        ]);
    }
}
