<?php

namespace App\Form\Frontend\ClienteDireccion;

use App\Entity\ClienteDireccion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Estado;
use App\Entity\Municipio;
use App\Entity\CodigoPostal;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class ClienteDireccionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entityManager = $options['entityManager'];
        $builder
            ->add('nombreCompleto', null, [
                'help' => 'nombre completo del destinatario (Nombre y apellidos)',
                'attr' => [
                    'class' => 'form-control form-control-sm'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingresa un nombre',
                    ]),
                    new Length([
                        'min' => 5,
                        'max' => 100,
                        'minMessage' => 'Tu nombre deberia contener minimo {{ limit }} caracteres',
                        // max length allowed by Symfony for security reasons
                        'maxMessage' => 'Tu nombre deberia contener maximo {{ limit }} caracteres',
                    ]),
                ],
            ])
            ->add('calle', null, [
                 'attr' => [
                    'class' => 'form-control form-control-sm'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingresa una calle',
                    ]),
                    new Length([
                        'min' => 5,
                        'max' => 100,
                        'minMessage' => 'Tu calle deberia contener minimo {{ limit }} caracteres',
                        // max length allowed by Symfony for security reasons
                        'maxMessage' => 'Tu calle deberia contener maximo {{ limit }} caracteres',
                    ]),
                ],
            ])
            ->add('numeroExterior', null, [
                'attr' => [
                    'class' => 'form-control form-control-sm'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingresa un numero',
                    ]),
                    new Length([
                        'min' => 1,
                        'max' => 6,
                        'minMessage' => 'Tu no. debe contener minimo {{ limit }} digitos',
                        // max length allowed by Symfony for security reasons
                        'maxMessage' => 'Tu no. debe contener maximo {{ limit }} digitos',
                    ]),
                ],
            ])
            ->add('numeroInterior', null, [
                'attr' => [
                    'class' => 'form-control form-control-sm'
                ]
            ])
            ->add('telefono', null, [
                'attr' => [
                    'class' => 'form-control form-control-sm'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingresa un telefono',
                    ]),
                    new Length([
                        'min' => 10,
                        'max' => 10,
                        'exactMessage' => 'Tu número debe contener {{ limit }} digitos',
                    ]),
                ],
            ])
            ->add('instrucionesEntrega', null, [
                'help' => 'Indicanos alguna informacion adicional sobre como entregar sus pedidos',
                'attr' => [
                    'class' => 'form-control form-control-sm'
                ],
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => 'Tus instrucciones deberian contener minimo{{ limit }} caracteres',
                        // max length allowed by Symfony for security reasons
                        'maxMessage' => 'TTus instrucciones deberian contener maximo {{ limit }} caracteres',
                    ]),
                ],
            ])
            ->add('Referencias', null,[
                'help' => 'Edificio, casa, departamento; Ejemplo: casa azul, frente a tienda',
                'attr' => [
                    'class' => 'form-control form-control-sm'
                ],
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => 'Tus referencias deberian contener minimo {{ limit }} caracteres',
                        // max length allowed by Symfony for security reasons
                        'maxMessage' => 'TTus referencias deberian contener maximo {{ limit }} caracteres',
                    ]),
                ],
            ])
            ->add('codigoPostalColoniaNoMapped', TextType::class,[
                'label' => 'Codigo postal',
                'mapped' => false,
                'attr' => ['class' => 'form-control-sm'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingresa un codigo postal',
                    ]),
                    new Length([
                        'min' => 5,
                        'max' => 5,
                        'exactMessage' => 'El codigo postal debe contener 5 numeros'
                    ]),
                ],
            ])
        ;
        $formModifier = function (FormInterface $form, $codigoPostal = null, $entityManager = null) {
            //$codigo = null === $codigoPostal ? [] : $codigoPostal->getCodigo();
            if(null !== $codigoPostal){
                $codigos = $entityManager->getRepository(CodigoPostal::class)->findBy(['codigo' => $codigoPostal]);
                if(!empty($codigos)){
                    $municipio = $entityManager->getRepository(CodigoPostal::class)->findOneBy(['codigo' => $codigoPostal]);
                    $form->add('codigoPostalColonia', EntityType::class, [
                        'label' => 'Colonia',
                        'attr' => [
                            'class' => 'form-control form-control-sm is-valid'
                        ],
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Por favor selecciona una colonia',
                            ]),
                        ],
                        'class' => 'App\Entity\CodigoPostal',
                        'placeholder' => 'Selecciona una colonia',
                        'choices' => $codigos,
                    ])
                    ->add('municipioNoMapped', TextType::class,[
                        'label' => 'Municipio',
                        'mapped' => false,
                        'attr' => ['class' => 'form-control-sm is-valid', 'value' => $municipio->getMunicipio()->getNombre(), 'disabled' => true],
                    ])
                    ->add('estadoNoMapped', TextType::class,[
                        'label' => 'Estado',
                        'mapped' => false,
                        'attr' => ['class' => 'form-control-sm is-valid', 'value' => $municipio->getMunicipio()->getEstado()->getNombre(), 'disabled' => true],
                    ])
                    ;
                }else{
                    $form->add('codigoPostalColonia', EntityType::class, [
                        'label' => 'Colonia',
                        'attr' => [
                            'class' => 'form-control form-control-sm'
                        ],
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Por favor selecciona una colonia',
                            ]),
                        ],
                        'class' => 'App\Entity\CodigoPostal',
                        'placeholder' => 'Codigo postal es invalido',
                        'choices' => [],
                    ])
                    ->add('municipioNoMapped', TextType::class,[
                        'label' => 'Municipio',
                        'mapped' => false,
                        'attr' => ['class' => 'form-control-sm is-invalid', 'disabled' => true, 'value' => 'Codigo postal invalido']
                    ])
                    ->add('estadoNoMapped', TextType::class,[
                        'label' => 'Estado',
                        'mapped' => false,
                        'attr' => ['class' => 'form-control-sm is-invalid', 'value' => 'Codigo postal invalido', 'disabled' => true],
                    ])
                    ;
                }
            }else{
                $form->add('codigoPostalColonia', EntityType::class, [
                    'label' => 'Colonia',
                    'attr' => [
                        'class' => 'form-control form-control-sm'
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Por favor selecciona una colonia',
                        ]),
                    ],
                    'class' => 'App\Entity\CodigoPostal',
                    'placeholder' => 'Ingresa un codigo postal',
                    'choices' => [],
                ])
                ->add('municipioNoMapped', TextType::class,[
                    'label' => 'Municipio',
                    'mapped' => false,
                    'attr' => ['class' => 'form-control-sm', 'disabled' => true],
                ])
                ->add('estadoNoMapped', TextType::class,[
                    'label' => 'Estado',
                    'mapped' => false,
                    'attr' => ['class' => 'form-control-sm', 'disabled' => true],
                ])
                ;
            }
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier, $entityManager) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();
                $municipio = $data->getCodigoPostalColonia();
                if($municipio){
                    $formModifier($event->getForm(), $municipio->getCodigo(), $entityManager);
                }else{
                    $formModifier($event->getForm(), null);
                } 
            }
        );

        $builder->get('codigoPostalColoniaNoMapped')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier, $entityManager) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $codigoPostal = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $codigoPostal, $entityManager);
            }
        );
        /* $formModifier = function (FormInterface $form, Estado $estado = null) {
            $municipios = null === $estado ? [] : $estado->getMunicipios();

            $form->add('municipio', EntityType::class, [
                'attr' => [
                    'class' => 'form-control form-control-sm'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor selecciona un municipio',
                    ]),
                ],
                'class' => 'App\Entity\Municipio',
                'placeholder' => '',
                'choices' => $municipios,
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();
                $municipio = $data->getMunicipio();
                if($municipio){
                    $formModifier($event->getForm(), $municipio->getEstado());
                }else{
                    $formModifier($event->getForm(), null);
                } 
            }
        );
        
        $builder->get('estado')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $estado = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $estado);
            }
        ); */
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('entityManager');
        $resolver->setDefaults([
            'data_class' => ClienteDireccion::class,
        ]);
    }
}
