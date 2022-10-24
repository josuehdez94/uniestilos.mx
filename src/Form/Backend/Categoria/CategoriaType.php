<?php

namespace App\Form\Backend\Categoria;

use App\Entity\Categoria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoriaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', null,[
                'required' => true,
                'attr' => [
                    'class' => 'form-control form-control-sm'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Categoria::class,
            'validation_groups' => ['backend_categoria_nueva', 'backend_categoria_editar']
        ]);
    }
}
