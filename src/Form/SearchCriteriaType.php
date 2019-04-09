<?php declare(strict_types=1);

namespace App\Form;

use App\Entity\SearchCriteria;
use App\Entity\Timetable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchCriteriaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var SearchCriteria $criteria */
        $criteria = $options['data'] ?? null;

        $builder
            ->add('timeFrom', TimeType::class, [
                'label' => false,
                'required' => false,
                'widget' => 'single_text',
                'data' => ($criteria && $criteria->isInitialised())
                    ? $criteria->getTimeFrom()
                    : new \DateTime(SearchCriteria::DEFAULT_TIME_FROM),
            ])
            ->add('timeTo', TimeType::class, [
                'label' => false,
                'required' => false,
                'widget' => 'single_text',
                'data' => ($criteria && $criteria->isInitialised())
                    ? $criteria->getTimeTo()
                    : new \DateTime(SearchCriteria::DEFAULT_TIME_TO),
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
