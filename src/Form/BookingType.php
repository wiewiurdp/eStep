<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\EventListener\AddFieldToDisabledInEditViewSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class BookingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('summary')
            ->add('start',
                DateTimeType::class,
                [
                    'widget' => 'single_text',
                ]
            )
            ->add('end',
                DateTimeType::class,
                [
                    'widget' => 'single_text',
                ]
            )
            ->add('description')
            ->add('location')
            ->add('attendeesJSON', HiddenType::class);

        $builder->addEventSubscriber(new AddFieldToDisabledInEditViewSubscriber());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
