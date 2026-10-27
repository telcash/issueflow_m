<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Model\Ticket;
use App\Service\InMemoryTicketCatalog;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InMemoryTicketCatalogTest extends TestCase
{
    #[Test]
    public function it_finds_an_existing_ticket(): void
    {
        $catalog = new InMemoryTicketCatalog();
        $ticket = $catalog->find('INC-1001');

        self::assertInstanceOf(Ticket::class, $ticket);
        self::assertSame('INC-1001', $ticket->getId());
    }

    #[Test]
    public function it_returns_null_when_the_ticket_does_not_exist(): void
    {
        $catalog = new InMemoryTicketCatalog();

        self::assertNull($catalog->find('INC-5555'));
    }

    #[Test]
    public function it_returns_the_current_catalog(): void
    {
        $catalog = new InMemoryTicketCatalog();

        self::assertCount(5, $catalog->getTickets());
    }
}