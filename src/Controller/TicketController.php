<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

final class TicketController
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
        /* Creo una variable tipo string, en principio vacía, para en ella almacenar
        el listado de todos los elementos <li> que contienen la información de cada
        uno de los tickets. Esta variable luego será insertada en el documento html */
        $itemsList = '';

        /* Recorro el arreglo de tickets con un bucle foreach y en cada iteración
        agrego a $itemsList un <li>
        Resultado del foreach paso a paso:
            $itemsList = ''
            1ra iteración:
            $itemsList = '<li>101 - Título: No puedo acceder - Prioridad: urgent - Estado: open</li>'
            2da iteración:
            $itemsList = '<li>101 - Título: No puedo acceder - Prioridad: urgent - Estado: open</li>
                            <li>102 - Título: Error en factura - Prioridad: high - Estado: in_progress</li>'
            3ra iteración:
            $itemsList = '<li>101 - Título: No puedo acceder - Prioridad: urgent - Estado: open</li>
                            <li>102 - Título: Error en factura - Prioridad: high - Estado: in_progress</li>
                            <li>103 - Título: Actualizar datos - Prioridad: normal - Estado: closed</li>'
            Fin del foreach
        */
        foreach(self::TICKETS as $ticket) {
            // .= es el operador de asignación y concatenación
            $itemsList .= "<li>{$ticket['id']} - Título: {$ticket['title']} 
                - Prioridad: {$ticket['priority']} - Estado: {$ticket['status']}</li>";
        }

        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>IssueFlow - Incidencias</title>
            </head>
            <body>
                <main>
                    <h1>Incidencias</h1>
                    <p>Total: </p>
                    <ul>{$itemsList}</ul>
                    <p><a href="/">Volver al inicio</a></p>
                </main>
            </body>
            </html>
        HTML;

        return new Response($html, Response::HTTP_OK);
    }
}
