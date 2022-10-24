<?php

namespace App\Form\Backend\User;

use App\Entity\RoleSistema;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RolesUsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rolesSistema', EntityType::class, [
                'class'     => RoleSistema::class,
                'expanded'  => true,
                'multiple'  => true,
            ])
            ->add('guardar', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
                'label' => 'Guardar'
            ])
        ; 
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'cascade_validation' => true
        ]);
    }
}
