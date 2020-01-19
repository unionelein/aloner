<?php declare(strict_types=1);

namespace App\Form\Type;

use App\Component\Util\Date;
use App\Entity\VO\SearchCriteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SearchCriteriaType extends AbstractType
{
    private const DEFAULT_DAY = 'now';

    private const DEFAULT_TIME_FROM = '18:00';

    private const DEFAULT_TIME_TO = '23:00';

    private const MIN_TIME_RANGE_MIN = 60;

    private const MIN_TIME_OFFSET_MIN = self::MIN_TIME_RANGE_MIN + 60;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('day', ChoiceType::class, [
                'label'   => false,
                'choices' => [
                    'Cегодня'     => Date::date(''),
                    'Завтра'      => Date::date('+1 day'),
                    'Послезавтра' => Date::date('+2 day'),
                ],
            ])
            ->add('timeFrom', TimeType::class, [
                'label'    => false,
                'required' => true,
                'widget'   => 'single_text',
            ])
            ->add('timeTo', TimeType::class, [
                'label'    => false,
                'required' => true,
                'widget'   => 'single_text',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'  => SearchCriteria::class,
            'constraints' => [new Callback([$this, 'validate'])],
            'data'        => $resolver->getDefinedOptions()['data'] ?? new SearchCriteria(
                Date::date(self::DEFAULT_DAY),
                Date::time(self::DEFAULT_TIME_FROM),
                Date::time(self::DEFAULT_TIME_TO)
            ),
            'translation_domain' => 'search_criteria_form',
        ]);
    }

    /**
     * @param SearchCriteria            $criteria
     * @param ExecutionContextInterface $context
     */
    public function validate(SearchCriteria $criteria, ExecutionContextInterface $context): void
    {
        if ($criteria->getTimeFrom() > $criteria->getTimeTo()) {
            $context->buildViolation('Начальное время больше конечного')->addViolation();

            return;
        }

        $minRangeMinutes = self::MIN_TIME_RANGE_MIN;

        if ($criteria->getTimeFrom()->modify("+{$minRangeMinutes} min") > $criteria->getTimeTo()) {
            $context->buildViolation('Минимальный промежуток времени - {{ min_range }} мин')
                ->setParameter('{{ min_range }}', $minRangeMinutes)
                ->addViolation();

            return;
        }

        $minOffsetMinutes = self::MIN_TIME_OFFSET_MIN;

        if ($criteria->getDay() == Date::date('') && Date::time("+{$minOffsetMinutes} min") > $criteria->getTimeTo()) {
            $context->buildViolation('Минимальный запас времени для поиска - {{ min_time }} мин')
                ->setParameter('{{ min_time }}', $minOffsetMinutes)
                ->addViolation();

            return;
        }
    }
}
