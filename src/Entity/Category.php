<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * Categorías
 */
#[ORM\Entity]
#[ORM\Table(name: 'categories')]
final class Category
{
    /**
     * Identificador técnico (id) de base de datos.
     * Comienza con un valor null porque una entidad recién
     * creada aún no ha sido insertada.
     * El valor de id será asignado al momento de persistirla
     */
    #[ORM\Id] // Indica que corresponde a la clave primaria (PK: Primary key)
    #[ORM\GeneratedValue] // Indica que es un valor autogenerado
    #[ORM\Column(type: Types::BIGINT, options: ['unsigned' => true])] // Si no especifico nombre de la columna, esta toma el nombre del atributo
    private ?int $id = null;

    /**
     * Relación OneToMany opcional para hacer la relación bidireccional
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'category')]
    private Collection $tickets;

    public function __construct(
        #[ORM\Column(length: 120, unique: true)]
        private string $name,
    )
    {
        // Normalizo el nombre quitando los espacios en blanco al principio y al final
        $normalizedName = trim($name);

        // Si el nombre normalizado está vacío lanzo un error
        if ($normalizedName === '') {
            throw new InvalidArgumentException('El nombre del usuario no puede estar vacío');
        }

        $this->name = $normalizedName;

        // Cree una instancia de $tickets
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    // En un objeto Category, ahora puedo acceder al listado de Tickets que pertenecen a esa categoría
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    /**
     * Mantenemos sincronizados ambos lados en memoria.
     *
     * Importante: Doctrine persiste la relación mirando el lado propietario,
     * que es Ticket::$category. Por eso este helper actualiza también Ticket.
     */
    // Agregar un ticket a la categoría
    public function addTicket(Ticket $ticket): void
    {
        // Si el ticket ya pertenece a la categoría no hacemos nada
        if ($this->tickets->contains($ticket)) {
            return;
        }

        // Agregamos el ticket al listado de la categoría
        $this->tickets->add($ticket);
        // Asignamos la categoría al elemento ticket
        $ticket->setCategory($this);
    }

    // Eliminar un ticket de la categoría
    public function removeTicket(Ticket $ticket): void
    {
        // Elimina el ticket del listado, si el ticket no estaba en el listado no hago nada y retorno
        if(!$this->tickets->removeElement($ticket)) {
            return;
        }

        // Sincronizo, asignando null a la categoría del ticket
        if ($ticket->getCategory() == $this) {
            $ticket->setCategory(null);
        }
    }
}