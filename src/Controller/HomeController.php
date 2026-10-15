<?php

// Verificamos estrictamente los tipos de las variables
declare(strict_types=1);

/* Agregamos el controlador al namespace correspondiente, para
que Symfony lo tenga registrado */

namespace App\Controller;

// ¡¡¡ Las importaciones se hacen al principio, antes de definir la clase !!!

// Importamos la clase Route de Symfony para definir rutas
use Symfony\Component\Routing\Attribute\Route;
// Importamos la clase Response de Symfony para crear los objetos de respuesta
use Symfony\Component\HttpFoundation\Response;

/* Para poder usar el método render() de la clase AbstractController,
   necesitamos primero importar la clase AbstractController, 
   y luego extender nuestra clase HomeController de ella.
*/
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/* Creamos la clase correspondiente a nuestro controlador
Tiene el mismo nombre que el archivo en src/Controller
Usamos el modificador final para que nadie herede de ella */

final class HomeController extends AbstractController
{
    /* Agregamos al controlador la ruta de la página de inicio 
    Argumentos del constructor de Route:
    1- URL de la ruta, en el caso de la página de inicio es : '/'
    2- Nombre interno de la ruta (esto luego será usado con Twig en las vistas)
        Se usa por convención app_home (app seguido de _ seguido del nombre del
        controlador)
    3- Método HTTP que atiende esta ruta: para nuestro caso método GET
        (Recordando: método GET se usa principalmente cuando solicitamos o 
        navegamos a una página sin enviar datos. Método POST lo usamos cuando enviamos
        por ejemplo datos de un formulario)
    */
    #[Route('/', name: 'app_home', methods: ['GET'])]
    /* Inmediatamente después de la ruta, definimos el método que aplica
    la lógica correspondiente y genera la respuesta (Response) para el cliente 
    Por convención, el método que atiende la ruta principal del controlador (o
    recurso) lleva el nombre index().
    Este método devuelve (return) un objeto de la clase Response de Symfony.
    */
    public function index(): Response
    {
        /* Retornar un objeto Response a través del metodo render().
        Argumentos de render():
        1- Plantilla a la que le envío la información.
        2- Un array asociativo que contiene los datos. En ese array las claves
        corresponden a los nombres de "variables" en Twig */
        return $this->render('home/index.html.twig', [
            'projectName' => 'IssueFlow',
            'message' => 'Symfony ha recibido la petición y ha devuelto una respuesta'
        ]);
    }

    /* Agregamos una nueva ruta, para el URL /health 
    En este caso le enviamos al cliente un mensaje en texto plano en el
    cuerpo de la respuesta. Hay que agregar la cabecera 'Content-Type'
    con la indicación que es texto plano lo que va en el cuerpo: text/plain.
    */
    #[Route('/health', name: 'app_health', methods: ['GET'])]
    public function health(): Response
    {
        return new Response(
            'IssueFlow OK',
            Response::HTTP_OK,
            ['Content-Type' => 'text/plain']
        );
    }

    /* Crear una ruta /saludo donde su método devuelva como respuesta
    en el cuerpo un documento  html que tenga una etiqueta <h1> con un
    pequeño saludo de prueba (Esto es un ejercicio de prueba, no estará en la app
    definitiva)
    #[Route('/saludo', name: 'app_saludo', methods: ['GET'])]
    public function saludo(): Response 
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Saludo</title>
            </head>
            <body>
                <main>
                    <h1>Hola a todos, este es mi saludo</h1>
                </main>
            </body>
            </html>
        HTML;

        return new Response($html, Response::HTTP_OK);
    } */
}
