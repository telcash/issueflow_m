<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Ticket;
use App\Model\TicketPriority;
use App\Model\TicketStatus;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Datos de entrada para editar una incidencia desde un formulario
 * Su responsabilidad es recibir los datos que Symfony Form transforma desde la
 * Request (petición), o sea, los datos que el usuario ha enviado desde el formulario
 * Usamos propiedades públicas deliberadamente, este objeto es una estructura simple
 * de entrada.
 */
final class TicketEditData
{
    #[Assert\NotBlank(
        message: 'El título es obligatorio',
        normalizer: 'trim'
    )]
    #[Assert\Length(
        max: 180,
        maxMessage: 'El título no puede superar los 180 caracteres',
        normalizer: 'trim'
    )]
    public string $title = '';

    public TicketPriority $priority = TicketPriority::Normal;
    public TicketStatus $status = TicketStatus::Open;
    public ?Category $category = null;
    public ?User $assignee = null;

    /**
     * Método que mapea un Ticket a un TicketEditData
     */
    public static function fromTicket(Ticket $ticket): self
    {
        // Creamos un nuevo dto para almacenar los datos del formulario
        $dto = new self();

        // Guardamos en el dto los datos del ticket que se quiere editar
        $dto->title = $ticket->getTitle();
        $dto->priority = $ticket->getPriority();
        $dto->status = $ticket->getStatus();
        $dto->category = $ticket->getCategory();
        $dto->assignee = $ticket->getAssignee();
        return $dto;
    }

    /**
     * Método que mapea (o aplica los cambios) a un Ticket
     */
    public function applyToTicket(Ticket $ticket): void
    {
        // Aplicamos al ticket los cambios del formulario
        $ticket->setTitle($this->title);
        $ticket->setPriority($this->priority);
        $ticket->setStatus($this->status);
        $ticket->setCategory($this->category);
        $ticket->assignTo($this->assignee);
    }
}