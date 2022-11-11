<?php

namespace App\Form\Backend\Articulo;

use App\Entity\Articulo;
use App\Entity\Categoria;
use App\Entity\Subcategoria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticuloType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categoria = $options['categoria'];
        $builder
            ->add('sku', null,[
                'attr' => ['class' => 'form-control form-control-sm']
            ])
            ->add('descripcion',null,[
                'help' => 'La descipcion debe contener al menos 10 caracteres y maximo 255.',
                'attr' => ['class' => 'form-control form-control-sm']
            ])
            ->add('precio1', null,[
                'attr' => ['class' => 'form-control form-control-sm']
            ])
            ->add('precio2',null,[
                'attr' => ['class' => 'form-control form-control-sm']
            ])
            ->add('activo', CheckboxType::class,[
                'required' => false,
                'help' => 'Al marcar este campo el articulo aparecera en la pagina principal.',
                'label' => '¿Activo?',
                'attr' => ['class' => 'form-check-input']
            ])
            ->add('sobrePedido', CheckboxType::class,[
                'required' => false,
                'help' => 'Al marcar este campo el articulo aparecera en la pagina principal para poder ser comprado sobre pedido.',
                'label' => '¿Sobre pedido?',
                'attr' => ['class' => 'form-check-input']
            ])
            ->add('clasificacion', ChoiceType::class, [
                'placeholder' => 'Selecciona',
                'attr' => ['class' => 'form-control form-control-sm'],
                'help' => 'Selecciona para que tipo de persona va dirigido este articulo.',
                'choices'  => [
                    'Hombre' => 'Hombre',
                    'Mujer' => 'Mujer',
                    'Niño' => 'Niña',
                    'Niña' => 'Niño',
                    'Unisex' => 'Unisex'
                ],
            ])
            
        ;
        $builder->add('categoria', EntityType::class, [
                'class' => Categoria::class,
                'placeholder' => 'Selecciona',
                'attr' => ['class' => 'form-control form-control-sm'],
                'mapped' => false,
                'data' => $categoria,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.nombre', 'ASC');
                },
                'choice_label' => 'nombre',
            ]) 
        ;

        $formModifier = function (FormInterface $form, Categoria $categoria = null) {
            $subcategorias = null === $categoria ? [] : $categoria->getSubcategorias();

            $form->add('subcategoria', EntityType::class, [
                'placeholder' => 'Selecciona',
                'attr' => [
                    'class' => 'form-control form-control-sm'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor selecciona una categoria',
                    ]),
                ],
                'class' => Subcategoria::class,
                'choices' => $subcategorias,
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();
                $subcategoria = $data->getSubcategoria();
                if($subcategoria){
                    $formModifier($event->getForm(), $subcategoria->getCategoria());
                }else{
                    $formModifier($event->getForm(), null);
                } 
            }
        );
        
        $builder->get('categoria')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $categoria = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $categoria);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('categoria');
        $resolver->setDefaults([
            'data_class' => Articulo::class,
            'validation_groups' => ['backend_articulo_nuevo', 'backend_articulo_editar']
        ]);
    }
}
