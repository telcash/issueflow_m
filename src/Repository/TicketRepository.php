<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contract\TicketCatalogInterface;
use App\Entity\Ticket;
use App\Form\Model\TicketSearchCriteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\OffsetPaginator;
use Doctrine\ORM\Tools\Pagination\Window;
use Doctrine\ORM\Tools\Pagination\WindowPage;
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

    /**
     * Método que devuelve una paágina de resultados dado unos criterios
     * $page indica cual es el número de página solicitada
     * $pageSize indica cuántos elementos hay en cada página
     */
    public function search(TicketSearchCriteria $criteria, int $page, int $pageSize): WindowPage
    {
        /**
         * Construimos una queryBuilder que pide todos los tickets,
         * agrega la información de category y assignee y ordena los tickets
         * por id de manera descendente
         */
        $queryBuilder = $this->createQueryBuilder('ticket')
            // Agregamos la información de category
            ->leftJoin('ticket.category', 'category')
            ->addSelect('category')
            // Agregamos la información de assignee
            ->leftJoin('ticket.assignee', 'assignee')
            ->addSelect('assignee')
            // Ordena por id de manera descendente
            ->orderBy('ticket.id', 'DESC');
        
        // Agregamos los criterios a $queryBuilder si son válidos

        // Verificar el criterio de búsqueda y normalizarlo
        // Operador ternario $var = condición ? valor si condición se cumple : valor si condición no se cumple
        $search = $criteria->query ? trim($criteria->query) : '';

        // Si hay un criterio de búsqueda lo agregamos
        if ($search !== '') {
            $queryBuilder
                // andWhere agrega un filtro o condición
                // LOWER convierte el texto a minúsculas
                // LIKE compara cadenas de texto a ver si son similares
                ->andWhere(
                    'LOWER(ticket.code) LIKE :search OR LOWER(ticket.title) LIKE :search'
                )
                // Fija el valor del parámatro search con el valor de nuestra variable $search
                // previamente, transforma $search a minúscula
                // por seguridad, insertamos los valores de nuestras variables con setParameter,
                // y no directamente en la instrucción andWhere
                ->setParameter('search', '%'.strtolower($search).'%');
        }

        // Aplicamos filtro por prioridad si no es null
        if ($criteria->priority !== null) {
            $queryBuilder
                ->andWhere('ticket.priority = :priority')
                ->setParameter('priority', $criteria->priority->value);
        }

        // Aplicamos filtro por status si no es null
        if ($criteria->status !== null) {
            $queryBuilder
                ->andWhere('ticket.status = :status')
                ->setParameter('status', $criteria->status->value);
        }

        // Aplicamos filtro por category si no es null
        if ($criteria->category !== null) {
            $queryBuilder
                ->andWhere('ticket.category = :category')
                ->setParameter('category', $criteria->category);
        }

        // Aplicamos filtro por assignee si no es null
        if ($criteria->assignee !== null) {
            $queryBuilder
                ->andWhere('ticket.assignee = :assignee')
                ->setParameter('assignee', $criteria->assignee);
        }

        /**
         * Del listado de resultados posibles solo solicito los correspondientes
         * a la página definida por $page, que serían máximo $pageSize resultados
         * OffsetPaginator crea un paginador basado en OFFSET y LIMIT
         * OFFSET le indica a la BD cúantos resultados debe "saltarse" del listado
         * LIMIT le indica a la BD cuántos resultados máximos debe retornar
         * fetchJoinCollection: false evita problemas habituales de paginación con relaciones OneToMany o ManyToMany
         * paginate() prepara y ejecuta la consulta paginada y devuelve el resultado
         * $queryBuilder es la consulta en sí que se quiere ejecutar
         * Window::fromPageNumberAndSize define la ventana de resultados: $page es el número de página
         * y $pageSize la cantidad de elementos por página
         */
        return (new OffsetPaginator(fetchJoinCollection: false))->paginate(
            $queryBuilder,
            Window::fromPageNumberAndSize($page, $pageSize)
        );
    }
}