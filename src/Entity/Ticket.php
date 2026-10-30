<?php

declare(strict_types=1);

namespace App\Entity;

use App\Model\TicketPriority;
use App\Model\TicketStatus;
use App\Repository\TicketRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * Entity Doctrine de una incidencia de IssueFlow
 * Sigue siendo un objeto PHP pero le añadiremos
 * el mapeo necesario para que Doctrine sepa como
 * persistirlo en la base de datos
 */
#[ORM\Entity(repositoryClass: TicketRepository::class)] // Decimos que la clase es también una entidad Doctrine y que usa TicketRepository
#[ORM\Table(name: 'tickets')] // Asignamos la tabla correspondiente en la Base de datos
final class Ticket
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
     * Para establecer la relación es obligatorio el mapeo en este lado, el lado del ManyToOne
     * Esta es una relación unidireccional. Ticket es el lado propietario de la relación
     * porque la FK assignee_id vive en la tabla de tickets.
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $assignee = null;

    /**
     * Para establecer la relación es obligatorio el mapeo en este lado, el lado del ManyToOne
     * Esta es una relación bidireccional. Ticket es el lado propietario de la relación
     * porque la FK category_id vive en la tabla de tickets.
     * Category::$tickets es el lado inverso y usa mappedBy="category".
     */
    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Category $category = null;

    /*
    Constructor Property Promotion (PHP moderno)
    */
    public function __construct(
        #[ORM\Column(length: 8, unique: true)]
        private readonly string $code, // readonly: solo lectura, una vez creado no puedo cambiar el valor de code

        #[ORM\Column(length: 180)]
        private string $title,

        #[ORM\Column(length: 20, enumType: TicketPriority::class)]
        private TicketPriority $priority,
        
        #[ORM\Column(length: 20, enumType: TicketStatus::class)]
        private TicketStatus $status
    )
    {
        /*  Validar el formato de code: INC- seguido de cuatro dígitos
            Para ello usamos una regex: regular expression (expresión regular): '/^INC-\d{4}$/'
            La función preg_match() compara una cadena de texto con la regex y devuelve 1
            si la cadena de texto es válida
        */
        if(preg_match('/^INC-\d{4}$/', $this->code) !== 1) {
            throw new InvalidArgumentException('El código no tiene un formato válido');
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
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
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

    // Desde Ticket puedo ver su assignee
    public function getAssignee(): ?User
    {
        return $this->assignee;
    }

    // Desde Ticket puedo ver su category
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    // Setters para los atributos de las relaciones
    // Asignamos un usuario a un ticket
    public function assignTo(?User $assignee): void
    {
        $this->assignee = $assignee;
    }
    // Asignamos una categoría a un ticket
    public function setCategory(?Category $category): void
    {
        $this->category = $category;
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