<?php

namespace App\Form;

use App\Component\VO\Sex;
use App\Entity\City;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => false,
            ])
            ->add('city', EntityType::class, [
                'label' => false,
                'class' => City::class,
                'placeholder' => 'Ваш город'
            ])
            ->add('sex', ChoiceType::class, [
                'label' => false,
                'placeholder' => 'Пол',
                'choices' => \array_flip(Sex::SEX),
            ])
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => false,
                'help' => 'Дата рождения'
            ])
            ->add('phone', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Номер телефона'
                ],
                'help' => 'Номер телефона нужен для записи (например занять место на квест для вас) и для связи с людьми, с которыми вы туда пойдете'
            ])
            ->add('acceptLicense', CheckboxType::class, [
                'label' => 'Я согласен с правилами сайта',
                'mapped' => false,
            ]);

        $builder->get('sex')->addModelTransformer(new CallbackTransformer(
            function (Sex $sex) {
                return $sex->toValue();
            },
            function (bool $sex) {
                return new Sex($sex);
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
