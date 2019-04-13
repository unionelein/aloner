<?php declare(strict_types=1);

namespace App\Form;

use App\Entity\SearchCriteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class SearchCriteriaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('timeFrom', TimeType::class, [
                'label' => false,
                'required' => true,
                'widget' => 'single_text',
                'constraints' => [new NotNull()],
            ])
            ->add('timeTo', TimeType::class, [
                'label' => false,
                'required' => true,
                'widget' => 'single_text',
                'constraints' => [new NotNull()],
            ])
            ->add('day', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Cегодня' => new \DateTime(),
                    'Завтра'  => new \DateTime('+1 day'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchCriteria::class,
        ]);
    }
}
