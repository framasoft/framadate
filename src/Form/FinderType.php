<?php

namespace Framadate\Form;

use Framadate\Entity\Poll;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class FinderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /**
             * Required attributes
             */
            ->add('adminMail', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'FindPolls.Send me my polls',
                'attr' => ['class' => 'btn-success']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'mapped' => false
                               ]);
    }
}
