<?php

namespace App\Form\Backend\Tallas;

use App\Entity\Tallas;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TallaType extends AbstractType
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
            ->add('guardar', SubmitType::class,[
                'attr' => ['class' => 'btn btn-primary']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tallas::class,
            'validation_groups' => ['backend_talla_nueva', 'backend_talla_editar']
        ]);
    }
}
