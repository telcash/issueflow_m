<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Ticket;
use App\Model\TicketPriority;
use App\Model\TicketStatus;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Comandos de apoyo para el catálogo de tickets.
 *
 * Symfony 8.1 permite declarar #[AsCommand] directamente sobre métodos
 * públicos. Usamos esta sintaxis moderna para un comando de carga inicial.
 */
final class TicketCommands
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TicketRepository $ticketRepository,
    ) {
    }

    /**
     * Crea los tickets de demostración que antes vivían en memoria.
     *
     * El comando es idempotente para la clase: si el code ya existe, no crea
     * un duplicado. Así puede ejecutarse de nuevo sin romper el UNIQUE(code).
     */
    #[AsCommand(
        name: 'app:ticket:seed',
        description: 'Carga los tickets iniciales de IssueFlow en la base de datos.',
    )]
    public function seed(OutputInterface $output): int
    {
        $definitions = [
            ['INC-1001', 'No puedo iniciar sesión', TicketPriority::Urgent, TicketStatus::Open],
            ['INC-1002', 'Error en la factura', TicketPriority::High, TicketStatus::InProgress],
            ['INC-1003', 'Actualizar datos de contacto', TicketPriority::Normal, TicketStatus::Closed],
        ];

        /** @var list<Ticket> $createdTickets */
        $createdTickets = [];

        foreach ($definitions as [$code, $title, $priority, $status]) {
            // Consultamos primero la base para no intentar duplicar el code UNIQUE.
            if ($this->ticketRepository->findByCode($code) !== null) {
                $output->writeln(sprintf('= %s ya existe; se omite.', $code));
                continue;
            }

            $ticket = new Ticket(
                code: $code,
                title: $title,
                priority: $priority,
                status: $status,
            );

            // persist() NO ejecuta todavía el INSERT. Registra la Entity en el
            // UnitOfWork para que Doctrine la sincronice posteriormente.
            $this->entityManager->persist($ticket);
            $createdTickets[] = $ticket;

            $output->writeln(sprintf('+ %s preparado para persistir.', $code));
        }

        // flush() sincroniza de una vez los cambios pendientes con la BD.
        $this->entityManager->flush();

        foreach ($createdTickets as $ticket) {
            $output->writeln(sprintf(
                '  %s persistido con id técnico %d.',
                $ticket->getCode(),
                $ticket->getId(),
            ));
        }

        $output->writeln(sprintf('Listo: %d ticket(s) creado(s).', count($createdTickets)));

        return Command::SUCCESS;
    }
}
