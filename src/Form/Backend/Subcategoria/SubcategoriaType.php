<?php

namespace App\Form\Backend\Subcategoria;

use App\Entity\Subcategoria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubcategoriaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subcategoria::class,
            'validation_groups' => ['backend_subcategoria_nueva', 'backend_subcategoria_editar']
        ]);
    }
}
