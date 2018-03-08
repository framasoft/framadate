<?php

namespace Framadate\Form;

use Framadate\Constraint\UniquePollConstraint;
use Framadate\Editable;
use Framadate\I18nWrapper;
use Framadate\Poll;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PollType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var I18nWrapper $i18n */
        $i18n = $options['i18n'];
        $builder
            /**
             * Required attributes
             */
            ->add('title', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('admin_mail', EmailType::class, [
                'constraints' => [
                    new Assert\Email(),
                ]
            ])
            ->add('admin_name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            /**
             * Optional attributes
             */
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('use_ValueMax', CheckboxType::class, [
                'label' => $i18n->get('Step 1', "Limit the ammount of voters per option"),
                'constraints' => [
                    new Assert\Type(['type' => 'bool']),
                ],
                'required' => false,
            ])
            ->add('ValueMax', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Type(['type' => 'integer']),
                    new Assert\Range(['min' => 0])
                ],
                'attr' => ['min' => 0],
            ])
            ->add('use_customized_url', CheckboxType::class, [
                'label' => $i18n->get('Step 1', 'Customize the URL'),
                'constraints' => [
                    new Assert\Type(['type' => 'bool']),
                ],
                'required' => false,
            ])
            ->add('id', TextType::class, [
                'constraints' => [
                    new Assert\Regex(['pattern' => POLL_REGEX]),
                    new UniquePollConstraint()
                ],
                'required' => false,
            ])
            ->add('use_password', CheckboxType::class, [
                'label' => $i18n->get('Step 1', "Use a password to restrict access"),
                'constraints' => [
                    new Assert\Type(['type' => 'bool']),
                ],
                'required' => false,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['attr' => ['class' => 'form-control']],
                'second_options' => ['attr' => ['class' => 'form-control']],
                'required' => false
            ])
            ->add('results_publicly_visible', CheckboxType::class, [
                'label' => $i18n->get('Step 1', "The results are publicly visible"),
                'constraints' => [
                    new Assert\Type(['type' => 'bool']),
                ],
                'required' => false,
            ])
            ->add('editable', ChoiceType::class, [
                'choices' => [
                    $i18n->get('Step 1', 'All voters can modify any vote') => Editable::EDITABLE_BY_ALL,
                    $i18n->get('Step 1', 'Voters can modify their vote themselves') => Editable::EDITABLE_BY_OWN,
                    $i18n->get('Step 1', 'Votes cannot be modified') => Editable::NOT_EDITABLE,
                ],
                'expanded' => true,
            ])
            ->add('receiveNewVotes', CheckboxType::class, [
                'constraints' => [
                    new Assert\Type(['type' => 'bool']),
                ],
                'required' => false,
            ])
            ->add('receiveNewComments', CheckboxType::class, [
                'constraints' => [
                    new Assert\Type(['type' => 'bool']),
                ],
                'required' => false,
            ])
            ->add('hidden', CheckboxType::class, [
                'label' => $i18n->get('Step 1', "Only the poll maker can see the poll's results"),
                'constraints' => [
                    new Assert\Type(['type' => 'bool']),
                ],
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => $i18n->get('Step 1', 'Go to step 2'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Poll::class,
            'i18n' => null,
                                   ]);
    }
}
