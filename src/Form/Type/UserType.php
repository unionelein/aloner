<?php declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\City;
use App\Entity\User;
use App\Entity\VO\Sex;
use App\Form\DataTransformer\ValueToSexTransformer;
use App\Validator\UserAgeRange;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label'       => false,
                'attr'        => ['placeholder' => 'Имя'],
                'constraints' => [
                    new Length([
                        'min' => 2,  'minMessage' => 'Слишком короткое имя',
                        'max' => 11, 'maxMessage' => 'Слишком длинное имя',
                    ]),
                ],
            ])
            ->add('city', EntityType::class, [
                'label'       => false,
                'class'       => City::class,
                'placeholder' => 'Ваш город',
            ])
            ->add('sex', ChoiceType::class, [
                'label'       => false,
                'placeholder' => 'Пол',
                'choices'     => \array_flip(Sex::SEX),
            ])
            ->add('birthday', DateType::class, [
                'widget'      => 'single_text',
                'format'      => 'dd/MM/yyyy',
                'label'       => false,
                'attr'        => ['placeholder' => 'Дата рождения'],
                'constraints' => [new UserAgeRange()],
            ])
            ->add('acceptLicense', CheckboxType::class, [
                'label'       => 'Я согласен с правилами сайта',
                'mapped'      => false,
                'constraints' => [
                    new IsTrue(['message' => 'Для использования сервиса вы должны согласиться с правилами']),
                ],
            ]);

        $builder->get('sex')->addModelTransformer(new ValueToSexTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
