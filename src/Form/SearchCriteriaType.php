<?php declare(strict_types=1);

namespace App\Form;

use App\Component\Util\Date;
use App\Entity\SearchCriteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SearchCriteriaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
                    'Cегодня'     => Date::date(''),
                    'Завтра'      => Date::date('+1 day'),
                    'Послезавтра' => Date::date('+2 day'),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchCriteria::class,
            'constraints' => [new Callback([$this, 'validate'])],
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

        if ($criteria->getDay() == Date::date('') && $criteria->getTimeTo() > Date::time('')) {
            $context->buildViolation('Конечное время сегодня уже прошло')->addViolation();

            return;
        }

        // TODO: add check on event duration and max available EP time before event.
    }
}
