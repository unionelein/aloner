<?php declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class PlaceOfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('place', TextType::class, [
                'label' => 'Место встречи:',
                'required' => true,
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                    new Length([
                        'max' => 20, 'maxMessage' => 'Сликшом длинное описание',
                        'min' => 4,  'minMessage' => 'Сликшом короткое описание',
                    ])
                ],
            ])
            ->add('preTime', ChoiceType::class, [
                'label' => 'Встречаемся за сколько минут до:',
                'required' => true,
                'choices' => [
                    '5 мин'  => 5,
                    '10 мин' => 10,
                    '15 мин' => 15,
                    '20 мин' => 20,
                    '30 мин' => 30
                ],
            ]);
    }
}