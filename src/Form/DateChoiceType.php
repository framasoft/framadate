<?php

namespace Framadate\Form;

use Framadate\Entity\DateChoice;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DateChoiceType extends ChoiceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /**
             * Required attributes
             */
            ->remove('name')
            ->add('date', DateType::class, [
                'format' => DateType::HTML5_FORMAT,
                'html5' => true,
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\Date(),
                ]
            ])
            ->add('moments', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'hours',
                    ],
                ],
                'required' => false,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                                   'data_class' => DateChoice::class,
                               ]);
    }
}
