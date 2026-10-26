<?php

function f1()
{
    f2(); // El código se va a f2() y espero
    echo "Hola, soy f1()" . PHP_EOL;
}
function f2()
{
    try {
        f3(); // El código se va a f3() y espero
    } catch (Throwable $t) {
        echo "Ocurrió un error en f2(): " . $t->getMessage() . PHP_EOL;
    }
    echo "Hola, soy f2()" . PHP_EOL;
}
function f3()
{
    f4(); // El código se va a f4() y espero
    echo "Hola, soy f3()" . PHP_EOL;
}
function f4()
{
    throw new RuntimeException("Falló la función f4()"); // Lanza un error / exception
    echo "Hola, soy f4()" . PHP_EOL;
}

/*  Estructura try catch para manejo de errores 
    El bloque try envuelve las operaciones riesgosas que pueden dar errores.
    El bloque catch se ejecuta si se lanza un error / exception en el bloque try
*/
try {
    f1(); // risky operation
} catch (Throwable $t) {
    echo "Ocurrió un error: " . $t->getMessage() . PHP_EOL;
}

echo "******* FIN DEL PROGRAMA *******" . PHP_EOL;