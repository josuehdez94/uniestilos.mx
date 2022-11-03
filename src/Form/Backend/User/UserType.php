<?php

namespace App\Form\Backend\User;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Unique;


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
                        'message' => 'Por favor ingresa un email',
                    ]),
                    new Email([
                        'message' => 'El email {{ value }} no es valido'
                    ]),
                    /* new Unique([
                        'message' => 'El email {{ value }} ya pertenece a una cuenta'
                    ]), */
                    /* new UniqueEntity([
                        'fields' => "email",
                        'errorPath' => "email",
                        'message' => "El email {{ value }} ya pertenece a una cuenta"
                    ]), */
                    new Length([
                        'min' => 3,
                        'max' => 50,
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
            'validation_groups' => ['backend_user_nuevo']
        ]);
    }
}
