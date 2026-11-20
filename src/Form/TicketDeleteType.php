<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para la eliminación de un ticket.
 * Consiste solamente en un botón de eliminar, por lo que no
 * necesita una estructura de datos para almacenar sus valores.
 */
final class TicketDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Construyo el formulario (solamente con un botón)
        $builder
            ->add('delete', SubmitType::class, [
                'label' => 'Eliminar incidencia',
                'attr' => [
                    'class' => 'button button-danger',
                ]
            ]);
    }

    /**
     * Configuramos las opciones del formulario:
     * Método de envío POST ya que hace modificaciones en el servidor
     * Activamos el token CSRF (Cross-site request forgery)
     * El formulario tiene un campo oculto con este token, el cual
     * es verificado por el servidor para asegurarse que la petición de legítima
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'csrf_protection' => true,
            'csrf_token_id' => 'delete_ticket'
        ]);
    }
}