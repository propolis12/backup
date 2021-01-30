<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FormUpdateUserInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('Username', TextType::class , [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Choose your username!'
                    ])
                ],
            ] )
            //neukladaj plain heslo do DB
            ->add('Password', PasswordType::class , [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Choose a password!'
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Password must have at least 5 characters'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'mapped' => false
            ] )
            ->add('firstname')
            ->add('lastname')
            //->add('registeredAt')
            //->add('agreedTermsAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
