<?php

namespace App\Form\Frontend\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class,[
                'attr' => [
                    'placeholder' => 'Nombre(s)'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingresa un nombre(s)',
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 20,
                        'minMessage' => 'Tu nombre deberia contener minimo {{ limit }} caracteres',
                        // max length allowed by Symfony for security reasons
                        'maxMessage' => 'Tu nombre deberia contener maximo {{ limit }} caracteres',
                    ]),
                ],
                
            ])
   
            ->add('apellidoPaterno', TextType::class,[
                'attr' => [
                    'placeholder' => 'Apellido paterno'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingresa un nombre(s)',
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 20,
                        'minMessage' => 'Tu nombre deberia contener minimo {{ limit }} caracteres',
                        // max length allowed by Symfony for security reasons
                        'maxMessage' => 'Tu nombre deberia contener maximo {{ limit }} caracteres',
                    ]),
                ],
                
            ])
            ->add('apellidoMaterno', TextType::class,[
                'attr' => [
                    'placeholder' => 'Apellido materno'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingresa un nombre(s)',
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 20,
                        'minMessage' => 'Tu nombre deberia contener minimo {{ limit }} caracteres',
                        // max length allowed by Symfony for security reasons
                        'maxMessage' => 'Tu nombre deberia contener maximo {{ limit }} caracteres',
                    ]),
                ],
                
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'Email'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingresa un nombre(s)',
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 50,
                        'minMessage' => 'Tu email deberia contener minimo {{ limit }} caracteres',
                        // max length allowed by Symfony for security reasons
                        'maxMessage' => 'Tu email deberia contener maximo {{ limit }} caracteres',
                    ]),
                ],
            ])
           
        ;
        $builder
            ->add('password', RepeatedType::class, [
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
