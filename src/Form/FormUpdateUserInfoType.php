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
                'required' => false,
            ] )
            //neukladaj plain heslo do DB
            ->add('Password', PasswordType::class , [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Password must have at least 5 characters'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'mapped' => false
            ] )
            ->add('firstname', null, [
                'required' => false,
            ])
            ->add('lastname', null, [
                'required' => false,
            ])
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
