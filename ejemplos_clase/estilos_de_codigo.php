<?php

/* Camel case: si el nombre es compuesto, cada palabra comienza con la letra en mayúscula,
no hay ningún caracter entre las palabras. La primera palabra comienza en minúscula para nombres
de variables, en mayúsculas para nombres de clases */

$nombreCompuestoVariable = "valor";
new NombreCompuestoClase();

/* Snake case: si el nombre es compuesto, cada palabra está separada 
por un guión bajo. Todas las letras son minúsculas */
$nombre_compuesto_variable = "valor";
new Nombre_Compuesto_Clase();