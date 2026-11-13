<?php

declare(strict_types=1);

namespace App\Api\Model;

use App\Entity\Ticket;
use App\Model\TicketPriority;
use App\Model\TicketStatus;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Payload de entrada para POST /api/tickets.
 * Esto no es una Entity, representa el contrato
 * JSON que acepta la API para crear una incidencia
 */

// Primero verificamos que no exista una incidencia con este código con el validador UniqueEntity
#[UniqueEntity(
    fields: ['code'],
    entityClass: Ticket::class,
    message: 'Ya existe una incidencia con este código',
    errorPath: 'code'
)]
final readonly class TicketCreatePayload
{
    public function __construct(

        #[Assert\NotBlank(
            message: 'El código es obligatorio',
            normalizer: 'trim',
        )]
        #[Assert\Regex(
            pattern: '/^INC-\d{4}$/',
            message: 'El código debe tener el formato INC-0000',
            normalizer: 'trim',
        )]
        public string $code,

        #[Assert\NotBlank(
            message: 'El título es obligatorio',
            normalizer: 'trim',
        )]
        #[Assert\Length(
            max: 180,
            maxMessage: 'El título no puede superar los 180 caracteres',
            normalizer: 'trim',
        )]
        public string $title,

        public TicketPriority $priority = TicketPriority::Normal,

        public TicketStatus $status = TicketStatus::Open,
    )
    {}
}