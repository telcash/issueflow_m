<?php

declare(strict_types=1);

namespace App\Tests\Model;

use App\Model\Ticket;
use App\Model\TicketPriority;
use App\Model\TicketStatus;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitarios del modelo Ticket
 * Un test unitario prueba una unidad pequeña de código de forma aislada
 */
final class TicketTest extends TestCase
{
    /**
     * Primer test: probar que cuando se crea un ticket, el título es normalizado.
     * Los tests tienen tres partes:
     * Arrange: preparación previa al test, en que contexto se realiza
     * Act: comportamiento a ejecutar
     * Assert: resultado que debe cumplirse
     */
    #[Test]
    public function it_normalizes_the_title_when_the_ticket_is_created(): void
    {
        // Preparamos los datos y construimos el objeto que queremos probar
        $ticket = new Ticket(
            id: 'INC-1001',
            title: '        No puedo iniciar sesión           ',
            priority: TicketPriority::Urgent,
            status: TicketStatus::Open,
        );
        // Título esperado: 'No puedo iniciar sesión'
        // Assert: verificamos el comportamiento observable esperado:
        self::assertSame('No puedo iniciar sesión', $ticket->getTitle());
    }

    /**
     * Segundo test: si intentamos crear un ticket con un título vacío
     * debe ocurrir un InvalidArgumentException
     */
    #[Test]
    public function it_rejects_an_empty_title(): void
    {
        // Tenemos que decir que esperamos un error antes que el error suceda
        $this->expectException(InvalidArgumentException::class);

        // Intentamos crear un ticket con un título vacío
        new Ticket(
            id: 'INC-1003',
            title: '',
            priority: TicketPriority::High,
            status: TicketStatus::Open,
        );
    }

    /**
     * Tercer test: si intentamos crear un ticket con un id inválido
     * debe ocurrir un InvalidArgumentException
     * Vamos a hacer uso de un data provider para probar varios ids inválidos:
     * Cada dataset del data provider es ejecutado como una prueba independiente
     * Indicamos luego de #[Test] el proveedor que queremos usar
     * Los parámetros de la función de prueba corresponden con la data
     * que suministra el data provider
     */
    public static function invalidIdentifierProvider(): iterable
    {
        // Emito cada dataset en un yield:
        yield 'not_inc_prefix' => ['1003'];
        yield 'too_many_digits' => ['INC-12345'];
        yield 'too_few_digits' => ['INC-123'];
        yield 'empty_identifier' => [''];
        yield 'lowercase_prefix' => ['inc-1001'];
    }
    #[Test]
    #[DataProvider('invalidIdentifierProvider')]
    public function it_rejects_an_invalid_identifier(string $invalidId): void
    {
        // Tenemos que decir que esperamos un error antes que el error suceda
        $this->expectException(InvalidArgumentException::class);

        // Intentamos crear un ticket con un id inválido
        new Ticket(
            id: $invalidId,
            title: 'Incidencia válida',
            priority: TicketPriority::High,
            status: TicketStatus::Open,
        );
    }

    #[Test]
    public function it_returns_all_ticket_values_through_getters(): void
    {
        $ticket = new Ticket(
            id: 'INC-1001',
            title: 'Error de acceso',
            priority: TicketPriority::Urgent,
            status: TicketStatus::Open,
        );

        self::assertSame('INC-1001', $ticket->getId());
        self::assertSame('Error de acceso', $ticket->getTitle());
        self::assertSame(TicketPriority::Urgent, $ticket->getPriority());
        self::assertSame(TicketStatus::Open, $ticket->getStatus());
    }

    #[Test]
    public function it_reports_whether_the_ticket_is_urgent_and_closed(): void
    {
        $ticket = new Ticket(
            id: 'INC-1003',
            title: 'Incidencia válida',
            priority: TicketPriority::Urgent,
            status: TicketStatus::Closed
        );
        self::assertTrue($ticket->isUrgent());
        self::assertTrue($ticket->isClosed());
    }
}
