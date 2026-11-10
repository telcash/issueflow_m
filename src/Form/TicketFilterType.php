<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Category;
use App\Form\Model\TicketSearchCriteria;
use App\Model\TicketPriority;
use App\Model\TicketStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TicketFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', SearchType::class, [ // SearchType crea un campo de barra de búsqueda
                'label' => 'Buscar',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Código o título'
                ]
            ])
            ->add('priority', EnumType::class, [
                'label' => 'Prioridad',
                'class' => TicketPriority::class,
                'choice_label' => 'label',
                'required' =>false,
                'placeholder' => 'Todas'
            ])
            ->add('status', EnumType::class, [
                'label' => 'Estado del ticket',
                'class' => TicketStatus::class,
                'choice_label' => 'label',
                'required' =>false,
                'placeholder' => 'Todos'
            ])
            ->add('category', EntityType::class, [
                'label' => 'Categoría',
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Todas'
            ])
            ->add('filter', SubmitType::class, [
                'label' => 'Filtrar'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TicketSearchCriteria::class,
            // GET se usa para lectura, los filtros viajan en la URL
            'method' => 'GET',
            // En peticiones GET no debemos colocar tokens CSRF para que no sea visible en URLs
            'csrf_protection' => false
        ]);
    }
}