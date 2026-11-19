<?php

declare(strict_types=1);

namespace App\Controller;

use App\Contract\TicketCatalogInterface;
use App\Contract\TicketSlaCalculatorInterface;
use App\Form\Model\TicketCreateData;
use App\Form\Model\TicketEditData;
use App\Form\Model\TicketSearchCriteria;
use App\Form\TicketDeleteType;
use App\Form\TicketEditType;
use App\Form\TicketFilterType;
use App\Form\TicketType;
use App\Service\TicketPaginator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TicketController extends AbstractController
{
    /* Constructor de TicketController 
    Cuando symfony levanta la aplicación, crea una instancia del controlador.
    Inyecto el servicio TicketCatalog
    Inyecto el servicio TicketSlaCalculator
    */
    public function __construct(
        private readonly TicketCatalogInterface $ticketCatalog,
        private readonly TicketSlaCalculatorInterface $slaCalculator,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
        private readonly TicketPaginator $ticketPaginator,
    )
    {}

   /* Crear la ruta /tickets que muestra el listado de todos los tickets
    Por convención se identifica con index a la ruta que muestra un listado
    del recurso (en este caso tickets)
    */
    #[Route('/tickets', name: 'app_ticket_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        /**
         * Leo el valor de 'last_ticket_code' almacenado en la sesión.
         * Asumimos en principio que no hay sesión activa y el valor es null
         */
        $lastTicketCode = null;

        // Verifico si hay una sesión activa
        if ($request->hasPreviousSession()) {
            // Sólo si hay una sesión activa intento recuperar el valor
            $lastTicketCode = $request->getSession()->get('last_ticket_code');
        }

        // Este objeto representa los criterios que llegan desde el formulario GET
        $criteria = new TicketSearchCriteria();

        // Creamos el objeto formulario
        $filterForm = $this->createForm(TicketFilterType::class, $criteria,[
            'action' => $this->generateUrl('app_ticket_index'),
        ]);

        // Proceso el formulario
        $filterForm->handleRequest($request);

        /**
         * Usar el servicio TicketPaginator para obtener la página
         * adecuada
         */
        $ticketPage = $this->ticketPaginator->getTicketPage($criteria, $request->query->get('page'));

        /**
         * Formulario general para eliminación de tickets
         */
        $deleteForm = $this->createForm(TicketDeleteType::class);

        return $this->render('ticket/index.html.twig', [
            'title' => 'Incidencias',
            'lastTicketCode' => $lastTicketCode,
            'filterForm' => $filterForm,
            'ticketPage' => $ticketPage,
            'deleteForm' => $deleteForm
        ]);
    }

    /**
     * GET: el usuario navega a la ruta, debo mostrarle la página del formulario
     * POST: el usuario envía el formulario, si está incorrecto le muestro nuevamente
     * el formulario con los mensajes de errores correspondientes, si está correcto
     * se hace una redirección a la página del ticket recién creado (ruta show)
     */
    #[Route('/tickets/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    // IsGranted solo permite acceso a la ruta a los usuarios que tengan el rol indicado
    #[IsGranted(
        'ROLE_AGENT',
        message: 'Necesitas permisos de agente para crear incidencias.'
    )]
    public function new(Request $request): Response
    {
        // Crear un objeto TicketCreateData (DTO) para recibir la información del formulario
        $data = new TicketCreateData();

        /**
         * Crear un objeto form con el método createForm() heredado de AbstractController
         * 1er parámetro corresponde a la clase que define el formulario
         * 2do parámetro corresponde al DTO que recibe la información del formulario
         */
        $form = $this->createForm(TicketType::class, $data);

        /**
         * El método handleRequest() hace lo siguiente:
         * 1- Detecta si el formulario ya ha sido enviado
         * 2- Extrae los datos correspondientes del Request
         * 3- Transforma los valores al tipo esperado
         * 4- Aisgna los datos al DTO (TicketCreateData)
         */
        $form->handleRequest($request);

        /**
         * Chequear si el estado del formulario es enviado y además si el formulario
         * es válido, en ese caso el formulario crea con éxito la incidencia en la
         * base de datos, y hago una redirección a la página del ticket recién creado.
         * isSubmitted() devuelve true si el estado del formulario es enviado
         * isValid() devuelve true si el formulario es válido, esto es, el formulario
         * pasa las validaciones establecidas en el DTO
         */
        if ($form->isSubmitted() && $form->isValid()) {
            // Creo la instancia Ticket a partir del DTO $data (TicketCreateData)
            $ticket = $data->mapToTicket();

            /**
             * Uso un EntityManager para persistir el ticket en la base de datos.
             * El EntityManager puede persistir entidades que ya han sido mapeadas.
             * persist() agrega a la unidad de trabajo la instrucción pero no la ejecuta
             * flush() ejecuta (consume) todas las instrucciones de la unidad de trabajo
             */
            $this->entityManager->persist($ticket);
            $this->entityManager->flush();

            /**
             * Un Flash Message vive en la sesión y está pensado para consumirse
             * una sola vez. Es ideal para confirmar una operación después de una
             * redirección
             */
            $this->addFlash(
                'success',
                sprintf('Incidencia %s creada correctamente.', $ticket->getCode())
            );

            // Luego de persistir el ticket en la BD, redirigimos a la página de detalle (show)
            return $this->redirectToRoute(
                'app_ticket_show', ['code' => $ticket->getCode()]
            );
        }
        /**
         * Aquí tenemos dos casos:
         * 1- El formulario no fue enviado -> muestro el formulario al cliente
         * 2- El formulario fue enviado pero no es válido -> muestro el formulario al cliente
         * pero con los errores de validación
         * En ambos casos devuelvo la plantilla, el objeto $form tiene la información
         * de los errores si los hay, y la plantilla los mostrará o no según sea el caso
         */
        return $this->render('ticket/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Ruta para edición de una incidencia.
     * GET: el usuario navega a la ruta, le enviamos el formulario de edición
     * POST: el usuario envía el formulario de edición con los datos y lo procesamos
     */
    #[Route('/tickets/{code}/edit', name: 'app_ticket_edit', methods: ['GET', 'POST'])]
    #[IsGranted(
        'ROLE_AGENT',
        message: 'Necesitas permisos de agente para modificar incidencias.'
    )]
    public function edit(string $code, Request $request): Response
    {
        // Solicitar el ticket a la base de datos
        $ticket = $this->ticketCatalog->findByCode($code);

        // Si el ticket no existe, lanzamos un error 404 (NOT_FOUND)
        if ($ticket === null) {
            throw $this->createNotFoundException('No existe la incidencia');
        }

        // Estructura de datos (dto) que almacena la información del formulario
        // La iniciamos con los datos del ticket en cuestión
        $data = TicketEditData::fromTicket($ticket);

        // Creamos el formulario tipo TicketEditType y asignamos $data para almacenar su información
        $form = $this->createForm(TicketEditType::class, $data);

        /**
         * Procesamos el formulario
         * El método handleRequest() hace lo siguiente:
         * 1- Detecta si el formulario ya ha sido enviado
         * 2- Extrae los datos correspondientes del Request
         * 3- Transforma los valores al tipo esperado
         * 4- Aisgna los datos al DTO (TicketCreateData)
         */
        $form->handleRequest($request);

        /**
         * Si el formulario fue enviado y es válido, guardamos los cambios en la base de datos
         * y redireccionamos al usuario al listado de tickets
         */
        if ($form->isSubmitted() && $form->isValid()) {
            // Aplicamos los cambios a la entidad $ticket
            $data->applyToTicket($ticket);
            
            // Hacemos un flush() para actualizar en la base de datos
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                sprintf('Incidencia %s actualizada correctamente.', $ticket->getCode())
            );

            // Completo el proceso redireccionando al usuario al listado de tickets
            return $this->redirectToRoute('app_ticket_index');
        }

        /**
         * Si el formulario no ha sido enviado o fue enviado pero no es válido,
         * le mostramos el formulario al cliente
         */
        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form
        ]);
    }

    #[Route('/tickets/{code}/delete', name: 'app_ticket_delete', methods: ['POST'])]
    #[IsGranted(
        'ROLE_AGENT',
        message: 'Necesitas permisos de agente para eliminar incidencias.'
    )]
    public function delete(string $code, Request $request)
    {
        // Buscamos la incidencia que se quiere eliminar en la base de datos
        $ticket = $this->ticketCatalog->findByCode($code);

        // Si el ticket no existe enviamos un error 404
        if ($ticket === null) {
            throw $this->createNotFoundException('No existe la incidencia');
        }

        /**
         * Creamos exactamente el mismo tipo de formulario que utilizamos en el listado
         */
        $form = $this->createForm(TicketDeleteType::class);

        // Procesamos el formulario
        $form->handleRequest($request);

        /**
         * Si el formulario fue enviado y es válido eliminamos la entidad
         * de la base de datos
         */
        if ($form->isSubmitted() && $form->isValid()) {
            // Marcamos la entidad para ser eliminada
            $this->entityManager->remove($ticket);

            // Ejecuto el DELETE en la base de datos
            $this->entityManager->flush();

            // Redirecciono al usuario al listado de tickets
            return $this->redirectToRoute('app_ticket_index');
        }

        // Si el formulario no fue enviado o no es válido lanzo un error de acceso denegado 403
        throw $this->createAccessDeniedException('Formulario de eliminación no válido');
    }


    /* Ruta dinámica que muestra la información de un ticket en particular 
    {code} representa una parte variable de la URL
    Ejemplo válido: /tickets/INC-1001
    Por convención se identifica con show a la ruta que muestra un recurso
    en particular (en este caso un ticket)
    */
    #[Route('/tickets/{code}', name: 'app_ticket_show', methods: ['GET'])]
    public function show(string $code, Request $request): Response
    {
        /* Buscamos el ticket en el listado de tickets, puede suceder
        que el ticket solicitado no exista */
        $ticket = $this->ticketCatalog->findByCode($code);

        /* Si $ticket es NULL la incidencia no existe */
        if ($ticket === NULL) {
            /* Guardo la información en el log para investigaciones posteriores */
            $this->logger->warning('Se solicitó una incidencia no existente', [
                'ticket_code' => $code,
                'route' => 'app_ticket_show',
            ]);

            throw $this->createNotFoundException('No existe la incidencia');
        }
        /**
         * Guardo en la sesión el código del último ticket visitado
         * getSession() me devuelve el objeto sesión, con los datos de la sesión actual
         * set() me permite guardar data en la sesión en la forma clave => valor
         */
        $request->getSession()->set('last_ticket_code', $ticket->getCode());

        /* Si $ticket no es NULL, envío a la plantilla la información
        de la incidencia */
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
            'responseTargetHours' => $this->slaCalculator->getResponseTargetHours($ticket)
        ]);
    }
}