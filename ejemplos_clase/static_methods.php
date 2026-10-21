<?php

declare(strict_types=1);

class Car
{
    public function __construct(private string $brand)
    {}

    /* Un método no static, debe ser invocado
    siempre por un objeto de la clase */
    public function getBrand()
    {
        return $this->brand;
    }

    /*
    Un método static puede ser invocado sin instanciar
    objetos de la clase.
    No puedo usar $this dentro de un método static, esto es,
    no puedo usar atributos ni métodos no static dentro de él.
    */
    public static function saludo()
    {
        return "Hola, soy un método static";
    }
}
// Llamada a un método estático
echo Car::saludo() . PHP_EOL;

// Lamada a un método no estático
$car1 = new Car('Citroen');
echo $car1->getBrand() . PHP_EOL;