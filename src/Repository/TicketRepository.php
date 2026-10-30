<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contract\TicketCatalogInterface;
use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

/**
 * Repository Doctrine de Ticket.
 * Extiende ServiceEntityRepository de Doctrine, el cual tiene métodos para hacer peticiones a la Base de datos
 * Implementa nuestro catálogo TicketCatalogInterface
 */
/* Al usar AsAlias, por defecto, el contenedor de servicios de Symfony inyectará esta clase
    cuando se solicite una implementación de TicketCatalogInterface */
#[AsAlias(TicketCatalogInterface::class)]
final class TicketRepository extends ServiceEntityRepository implements TicketCatalogInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        // El repositorio queda especializado para la Entity Ticket
        parent::__construct($registry, Ticket::class);
    }

    /* Método que devuelva un array con todas las incidencias */
    public function getTickets(): array
    {
        /*
            El método findBy() de ServiceEntityRepository, devuelve un listado de la base de datos.
            El primer argumento es un array de condiciones, si lo dejo vacío es porque no hay condiciones.
            El segundo argumento indica el orden en que voy a recibir el listado
        */
        return $this->findBy([], ['id' => 'ASC']);
    }

    /* Método que busca una incidencia en el listado según un
    code proporcionado, y que devuelva null si no existe */
    public function findByCode(string $code): ?Ticket
    {
        /*
            El método findOneBy() encuentra el primer elemento en la base de
            datos que cumple con las condiciones indicadas
        */
        return $this->findOneBy(['code' => $code]);
    }
}