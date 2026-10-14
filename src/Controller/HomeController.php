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

/* Creamos la clase correspondiente a nuestro controlador
Tiene el mismo nombre que el archivo en src/Controller
Usamos el modificador final para que nadie herede de ella */
final class HomeController {
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
    public function index(): Response {
        // Creamos las variables $projectName y $message
        $projectName = 'IssueFlow';
        $message = 'Symfony ha recibido la peticón y ha devuelto una respuesta';

        /* Creamos la variable $html que contiene el cuerpo de la respuesta, en
        este caso un documento HTML */
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>{$projectName}</title>
            </head>
            <body>
                <main>
                    <h1>{$projectName}</h1>
                    <p>{$message}</p>
                    <p><a href="/tickets">Ver incidencias</a></p>
                    <p><a href="/health">Comprobar estado</a></p>
                </main>
            </body>
            </html>
        HTML;

        /* Devolvemos el objeto Response con $html como cuerpo (body) y un código 200 (OK)
        Usamos la constante Response::HTTP_OK que corresponde a 200 para que el código
        sea más declaritivo */
        return new Response($html, Response::HTTP_OK);
    }

    /* Agregamos una nueva ruta, para el URL /health 
    En este caso le enviamos al cliente un mensaje en texto plano en el
    cuerpo de la respuesta. Hay que agregar la cabecera 'Content-Type'
    con la indicación que es texto plano lo que va en el cuerpo: text/plain.
    */
    #[Route('/health', name: 'app_health', methods: ['GET'])]
    public function health(): Response {
        return new Response(
            'IssueFlow OK',
            Response::HTTP_OK,
            ['Content-Type' => 'text/plain']
        );
    }
}