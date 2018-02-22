<?php

namespace Framadate\Form;

use Framadate\Entity\DateChoice;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
            ->remove('name')
            ->add('name', DateType::class, [
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
            ->add('moments', CollectionType::class, [
                'entry_type' => MomentType::class,
                'entry_options' => [
                    'label' => false,
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
