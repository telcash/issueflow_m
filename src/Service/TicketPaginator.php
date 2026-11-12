<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\TicketCatalogInterface;
use App\Form\Model\TicketSearchCriteria;
use Doctrine\ORM\Tools\Pagination\WindowPage;

final class TicketPaginator
{
    private const PAGE_SIZE = 3;

    public function __construct(
        private readonly TicketCatalogInterface $ticketCatalog,
    )
    {}
    /**
     * Método que dado un string $page y un TicketSearchCriteria, me devuelve la página adecuada.
     * Si $page no puede ser convertido a entero me debe devolver la primera página
     * Si $page es menor que 1 me debe devolver la primera página
     * Si $page es mayor que el valor de la útlima página, me debe devolver la última página
     * En cualquier otro caso me devuelve la página solicitada.
     */
    public function getTicketPage(TicketSearchCriteria $criteria, ?string $page): WindowPage
    {
        /**
         * Obtiene la página adecuada según el valor de $page, usa filter_var() para validar
         * que es un número entero, si la validación falla le asigna el valor por defecto de 1
        */  
        $page = filter_var($page, FILTER_VALIDATE_INT, ['options' => ['default' => 1]]);

        // Me aseguro que $page sea igual o mayor a 1 (no puede ser menor que 1)
        $page = max(1, $page);

        // Pido la página al repositorio, establezco un tamaño de página
        $ticketPage = $this->ticketCatalog->search($criteria, $page, self::PAGE_SIZE);

        /**
         * getPageNumber() me devuelve el número de la página
         * getPageCount() me devuelve el número total de páginas (según la división que hizo el paginador)
         */
        if ($ticketPage->getPageNumber() > $ticketPage->getPageCount()) {
            $ticketPage = $this->ticketCatalog->search(
                $criteria,
                $ticketPage->getPageCount(),
                self::PAGE_SIZE
            );
        }

        return $ticketPage;
    }
}