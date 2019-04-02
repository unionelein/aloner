<?php declare(strict_types=1);

namespace App\Form;

use App\Entity\SearchCriteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchCriteriaType extends AbstractType
{
    private const DEFAULT_TIME_FROM = '18:00';

    private const DEFAULT_TIME_TO = '23:00';

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
                    : new \DateTime(self::DEFAULT_TIME_FROM),
            ])
            ->add('timeTo', TimeType::class, [
                'label' => false,
                'required' => false,
                'widget' => 'single_text',
                'data' => ($criteria && $criteria->isInitialised())
                    ? $criteria->getTimeTo()
                    : new \DateTime(self::DEFAULT_TIME_TO),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchCriteria::class,
        ]);
    }
}
