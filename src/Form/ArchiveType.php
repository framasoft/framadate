<?php

namespace Framadate\Form;

use Framadate\Entity\Poll;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ArchiveType extends AbstractType
{
    /**
     * @var int
     */
    private $default_poll_duration;

    /**
     * ArchiveType constructor.
     * @param int $default_poll_duration
     */
    public function __construct(int $default_poll_duration)
    {
        $this->default_poll_duration = $default_poll_duration;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /**
             * Required attributes
             */
            ->add('end_date', DateType::class, [
                'format' => DateType::HTML5_FORMAT,
                'widget' => 'single_text',
                'html5' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Date(),
                    new Assert\GreaterThan("tomorrow"),
                    new Assert\LessThan("+12 months")
                ],
                'data' => (new \DateTime())->modify('+' . $this->default_poll_duration . ' days'),
                'attr' => ['class' => 'form-control']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Step 3.Create the poll',
                'attr' => ['class' => 'btn-success'],
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
