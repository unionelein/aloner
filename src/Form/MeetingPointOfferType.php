<?php declare(strict_types=1);

namespace App\Form;

use App\Component\Model\DTO\Form\MeetingPointData;
use App\Component\Util\Date;
use App\Entity\Event;
use App\Entity\EventParty;
use App\Entity\Timetable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class MeetingPointOfferType extends AbstractType
{
    private const MEETING_TIME_OFFSET_BEFORE_EVENT = 10;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var EventParty $eventParty */
        $eventParty = $options['eventParty'];
        $today      = Date::date(new \DateTime());

        $builder
            ->add('day', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Cегодня' => $today,
                    'Завтра'  => (clone $today)->modify('+1 day'),
                ],
                'data' => $eventParty->getUsersSearchCriteriaDate(),
            ])
            ->add('time', TimeType::class, [
                'label' => false,
                'required' => true,
                'widget' => 'single_text',
                'constraints' => [new NotNull(['message' => 'Выберите время'])],
                'data' => $eventParty->generateMeetingAt(),
            ])
            ->add('place', TextType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotNull(['message' => 'Введите место']),
                    new Length([
                        'max' => 30, 'maxMessage' => 'Сликшом длинное описание',
                        'min' => 4,  'minMessage' => 'Сликшом короткое описание',
                    ])
                ],
                'data' => $eventParty->getEvent()->getAddress(),
            ])
            ->add('rejectedOfferId', HiddenType::class, [
                'mapped' => false,
                'data' => $options['rejectedOfferId'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MeetingPointData::class,
            'rejectedOfferId' => null,
        ]);

        $resolver->setRequired(['eventParty']);
    }
}