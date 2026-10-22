<?php

declare(strict_types=1);

namespace App\Model;

use InvalidArgumentException;

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
        private TicketPriority $priority,
        private TicketStatus $status
    )
    {
        /*  Validar el formato de id: INC- seguido de cuatro dígitos
            Para ello usamos una regex: regular expression (expresión regular): '/^INC-\d{4}$/'
            La función preg_match() compara una cadena de texto con la regex y devuelve 1
            si la cadena de texto es válida
        */
        if(preg_match('/^INC-\d{4}$/', $this->id) !== 1) {
            throw new InvalidArgumentException('El id no tiene un formato válido');
        }

        /* Normalización de title: Eliminar espacios en blanco antes y después 
        La función trim() elimina estos espacios
        */
        $normalizedTitle = trim($title);

        /* Verifico que $normalizedTitle no sea una cadena de texto vacía */
        if ($normalizedTitle === '') {
            throw new InvalidArgumentException('El título no puede estar vacío');
        }

        $this->title = $normalizedTitle;
    }

    /* Creamos los getters para cada uno de los atributos */
    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPriority(): TicketPriority
    {
        return $this->priority;
    }

    public function getStatus(): TicketStatus
    {
        return $this->status;
    }

    /* Método que devuelve true si el ticket tiene una
    prioridad 'urgent', y false en caso contrario */
    public function isUrgent(): bool
    {
        return $this->priority->isUrgent();
    }

    /* Método que devuelve true si una incidencia está cerrada */
    public function isClosed(): bool
    {
        return $this->status->isClosed();
    }
}