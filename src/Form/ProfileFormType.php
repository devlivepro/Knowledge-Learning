<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label'    => 'Nom d’utilisateur',
                'disabled' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
            ])
            ->add('firstName', TextType::class, [
                'label'    => 'Prénom',
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label'    => 'Nom',
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'label'       => 'Téléphone',
                'required'    => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^(\+33|0)[1-9](\d{2}){4}$/',
                        'message' => 'Veuillez entrer un numéro de téléphone valide (10 chiffres).',
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'label'    => 'Adresse',
                'required' => false,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'mapped'          => false,
                'required'        => false,
                'first_options'   => [
                    'label'       => 'Nouveau mot de passe',
                    'attr'        => ['class' => 'form-control'],
                    'constraints' => [
                        new Length([
                            'min'        => 8,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                            // maximum managed by Symfony
                            'max'        => 4096,
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                            'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.',
                        ]),
                    ],
                ],
                'second_options'  => [
                    'label' => 'Confirmez le mot de passe',
                    'attr'  => ['class' => 'form-control'],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}