<?php

namespace Framadate\Form;

use Framadate\Entity\Poll;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PollDateChoicesType extends PollChoicesType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            /**
             * Required attributes
             */
            ->remove('choices')
            ->add('choices', CollectionType::class, [
                'entry_type' => DateChoiceType::class,
                'entry_options' => [
                    'label' => false,
                    'required' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'constraints' => [
                    new Assert\Count(['min' => 0, 'max' => $this->maxChoices]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                                   'data_class' => Poll::class,
                               ]);
    }
}
