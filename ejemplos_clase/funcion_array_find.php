<?php

$medidas = [32.6, 15.8, 10.3, 17.9];

/* array_find(), disponible en PHP 8.4+ devuelve el primer elemento
del array que cumple con la condición indicada por la función callback.
Si no encuentra ningún elemento que cumpla la condición devuelve null */

/* Condición: mayor que 17 -> 32.6
    Condición: menor que 12 -> 10.3
    Condición: igual a 15.8 -> 15.8
    Condición: mayor que 100 -> null */

/* 1er argumento de array_find(): El array donde se va a realizar la búsqueda
2do argumento es una función callback que prueba la condición para cada uno de los
elementos
*/
$medida = array_find(
    $medidas,
    fn ($medida) => $medida > 100
);

var_dump($medida);