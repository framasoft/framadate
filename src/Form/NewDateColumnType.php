<?php

namespace Framadate\Form;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class NewDateColumnType extends NewColumnType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            /**
             * Required attributes
             */
            ->remove('title')
            ->add('title', DateType::class, [
                'format' => DateType::HTML5_FORMAT,
                'widget' => 'single_text',
                'html5' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Date(),
                ],
                'label' => 'Generic.Day',
                'label_attr' => ['class' => 'col-md-4'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('moments', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'label' => 'Generic.Time',
                'label_attr' => ['class' => 'col-md-4'],
                'attr' => ['class' => 'form-control']
            ])
        ;
    }
}
