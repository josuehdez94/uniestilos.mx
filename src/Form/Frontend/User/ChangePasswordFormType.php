<?php

namespace App\Form\Frontend\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('oldPassword', PasswordType::class, 
                [
                    'label' => 'Contraseña actual',
                    'constraints' => [
                        new SecurityAssert\UserPassword([
                            'message' => 'La contraseña que ingreso no coincide con la contraseña actual'
                        ]),
                        new Length([
                            'min' => 8,
                            'max' => 10,
                            'minMessage' => 'Tu contraseña deberia contener minimo {{ limit }} caracteres',
                            // max length allowed by Symfony for security reasons
                            'maxMessage' => 'Tu contraseña deberia contener maximo {{ limit }} caracteres',
                        ]),
                    ]
                ]);
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Por favor ingresa una nueva contraseña',
                        ]),
                        new Length([
                            'min' => 8,
                            'max' => 10,
                            'minMessage' => 'Tu contraseña deberia contener minimo {{ limit }} caracteres',
                            // max length allowed by Symfony for security reasons
                            'maxMessage' => 'Tu contraseña deberia contener maximo {{ limit }} caracteres',
                        ]),
                    ],
                    'label' => 'Nueva contraseña',
                ],
                'second_options' => [
                    'label' => 'Repetir contraseña',
                ],
                'invalid_message' => 'Las contraseñas no coinciden.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'validation_groups' => ['backend_change_contrasena']
        ]);
        
    }
}
