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


class CambiarUsuarioType extends AbstractType
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
