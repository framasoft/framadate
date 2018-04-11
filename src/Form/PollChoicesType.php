<?php

namespace Framadate\Form;

use Framadate\Entity\Poll;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PollChoicesType extends AbstractType
{
    /**
     * @var int
     */
    protected $maxChoices;

    /**
     * PollChoicesType constructor.
     * @param int $maxChoices
     */
    public function __construct(int $maxChoices)
    {
        $this->maxChoices = $maxChoices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /**
             * Required attributes
             */
            ->add('choices', CollectionType::class, [
                'entry_type' => ChoiceType::class,
                'entry_options' => [
                    'required' => false,
                ],
                'label' => false,
                'constraints' => [
                    new Assert\Count(['min' => 0, 'max' => $this->maxChoices]),
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['title' => 'Step 2.Go to step 3', 'class' => 'btn-success disabled'],
                'label' => 'Generic.Next',
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
