<?php

namespace App\Form;

use App\Entity\Attendee;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttendeeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('surname')
            ->add('mail')
            ->add('address')
            ->add('batches')
            ->add('roles', EntityType::class,
                [
                    'class' => Role::class,
                    'choice_label' => function ($roles) {
                        return $roles->getName() . ' - ' . $roles->getBatch();
                    },
                    'multiple' => true,
                ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Attendee::class,
        ]);
    }
}
