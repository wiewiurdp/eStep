<?php

declare(strict_types = 1);

namespace App\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddFieldToDisabledInEditViewSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (!$data || !$data->getId()) {
            $form->add('recurrence', ChoiceType::class, [
                'attr' => [
                    'class' => 'reccurence',
                ],
                'choices' => [
                    'brak' => null,
                    'codziennie' => 'DAILY',
                    'co tydzień' => 'WEEKLY',
                    'co miesiąc' => 'MONTHLY',
                ],
            ])
                ->add('recurrenceFinishedOn',
                    DateTimeType::class,
                    [
                        'required' => false,
                        'widget' => 'single_text',
                        'attr' => [
                            'class' => 'recurrenceFinishedOn',
                        ],
                    ]
                );
        } else {
            $form->add('recurrence', ChoiceType::class,
                [
                    'disabled' => true,
                    'placeholder' => $data->getRecurrence(),
                ]
            );
            $form->add('recurrenceFinishedOn', DateTimeType::class,
                [
                    'disabled' => true,
                    'widget' => 'single_text',
                    'placeholder' => $data->getRecurrenceFinishedOn(),
                ]
            );
        }
    }
}
