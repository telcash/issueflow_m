<?php

declare(strict_types=1);

namespace App\Model;

/*
Ticket representa una incidencia en IssueFloew como
un objeto PHP
*/
final class Ticket
{
    /*
    Constructor Property Promotion (PHP moderno)
    */
    public function __construct(
        private string $id,
        private string $title,
        private string $priority,
        private string $status
    )
    {}

    /* Creamos los getters para cada uno de los atributos */
    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /* Mock data de tickets: Data simulada de prueba. La data real
    se obtendrá luego de una petición a la base de datos. 
    La data se generará con un método estático que devuelva un array de tickets.
    */
    public static function getAllTickets(): array
    {
        return [
            new Ticket(
                id: 'INC-1001',
                title: 'No puedo acceder',
                priority: 'urgent',
                status: 'open'
            ),
            new Ticket(
                id: 'INC-1002',
                title: 'Error en factura',
                priority: 'high',
                status: 'in_progress'
            ),
            new Ticket(
                id: 'INC-1003',
                title: 'Actualizar datos',
                priority: 'normal',
                status: 'closed'
            ),
            new Ticket(
                id: 'INC-1004',
                title: 'El ordenador está en llamas',
                priority: 'urgent',
                status: 'in_progress'
            )
        ];
    }

    /* Metodo find que encuentra un ticket en el listado, dado su id 
    ?Ticket quiere decir que el método puede devolver o un Ticket o null
    */
    public static function find(string $id): ?Ticket
    {
        /* array_find(), disponible en PHP 8.4+ devuelve el primer elemento
        del array que cumple con la condición indicada por la función callback.
        Si no encuentra ningún elemento que cumpla la condición devuelve null */
        return array_find(
            Ticket::getAllTickets(),
            fn (Ticket $ticket): bool => $ticket->getId() === $id
        );
    }
}