<?php declare(strict_types=1);

namespace App\Form;

use App\Component\Util\Date;
use App\Entity\EventParty;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class MeetingPointOfferType extends AbstractType
{
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
                    'Завтра'  => $today->modify('+1 day'),
                ],
                'data' => $eventParty->getUsersSearchCriteriaDate(),
            ])
            ->add('time', TimeType::class, [
                'label' => false,
                'required' => true,
                'widget' => 'single_text',
                'constraints' => [new NotNull()],
                'data' => $eventParty->getMeetingAt(),
            ])
            ->add('place', TextType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new NotNull(),
                    new Length([
                        'max' => 30, 'maxMessage' => 'Сликшом длинное описание',
                        'min' => 4,  'minMessage' => 'Сликшом короткое описание',
                    ])
                ],
                'data' => $eventParty->getMeetingPlace(),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['eventParty']);
    }
}