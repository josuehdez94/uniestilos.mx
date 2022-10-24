<?php

namespace App\Form\Frontend\Carrito;

use App\Entity\ArticuloTalla;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\DocumentoRegistro;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AgregarArticuloSobrePedidoCarritoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$entityManager = $options['entityManager'];
        $cantidades = [];
        for ($i=1; $i <100 ; $i++) { 
            $cantidades [] = $i;
        }
        $articulo = $options['articulo'];
        $builder
            ->add('articuloTalla', EntityType::class, [
                'class' => ArticuloTalla::class,
                'attr' => ['class' => 'form-select sizes'],
                'query_builder' => function (EntityRepository $er) use($articulo) {
                    return $er->createQueryBuilder('at')
                        ->innerJoin('at.talla', 'talla')
                        ->where('at.articulo = :articulo')
                        //->andWhere('at.activa = true')
                        //->andWhere('at.existencia > 0')
                        ->setParameter('articulo', $articulo)
                        ->orderBy('at.id', 'ASC');
                },
                'choice_label' => 'talla.nombre',
                'choice_attr' => ChoiceList::attr($this, function(?ArticuloTalla $articuloTalla){
                    return $articuloTalla ? ['data-id' => 5] : [];
                })
            ])
            ->add('cantidad', ChoiceType::class, [
                'attr' => ['class' => 'form-select quantity'],
                'choices'  => $cantidades
            ])
            ->add('precio', HiddenType::class,[
                'data' => $articulo->getPrecio1()
            ])
            ->add('agregar', SubmitType::class,[
                'label' => 'Agregar al carrito',
                'attr' => ['class' => 'btn btn-primary mt-2']
            ])
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //$resolver->setRequired('entityManager');
        $resolver->setRequired('articulo');
        $resolver->setDefaults([
            'data_class' => DocumentoRegistro::class,
            'validation_groups' => ['front_agregar_articulo_carrito']
        ]);
    }
}
