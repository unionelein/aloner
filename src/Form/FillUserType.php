<?php

namespace App\Form;

use App\Component\DTO\Entity\UserDTO;
use App\Component\VO\Sex;
use App\Entity\City;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FillUserType extends AbstractType
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
                'label' => false,
                'help' => 'Дата рождения'
            ])
            ->add('phone', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Телефон'
                ],
                'help' => 'Телефон будет виден только людям, с которыми вы куда-нибудь пойдете'
            ])
            ->add('acceptLicense', CheckboxType::class, [
                'label' => 'Я согласен с правилами сайта',
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserDTO::class,
        ]);
    }
}
