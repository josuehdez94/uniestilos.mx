<?php

namespace App\Form\Backend\ArticuloTalla;

use App\Entity\Articulo;
use App\Entity\Categoria;
use App\Entity\Subcategoria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AsignarTallaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$categoria = $options['categoria'];
        $builder
            ->add('tallas', CollectionType::class,[
                'entry_type' => \App\Form\Backend\ArticuloTalla\ArticuloTallaType::class
            ])            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //$resolver->setRequired('categoria');
        $resolver->setDefaults([
            'data_class' => Articulo::class,
            //'validation_groups' => ['backend_articulo_nuevo', 'backend_articulo_editar']
        ]);
    }
}
