<?php

namespace App\Form\Frontend\Carrito;

use App\Entity\ClienteDireccion;
use App\Entity\Documento;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SeleccionarDireccionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $carrito = $options['documento'];
        $cliente = $carrito->getCliente();
        $builder
            ->add('clienteDireccion', EntityType::class, [
                'class' => ClienteDireccion::class,
                'label' => 'Direccion',
                'placeholder' => 'Selecciona una dirección para tu envio',
                //'attr' => ['class' => 'form-select form-select-sm'],
                'query_builder' => function (EntityRepository $er) use($cliente) {
                    return $er->createQueryBuilder('cd')
                        ->andWhere('cd.cliente = :cliente')
                        ->setParameter('cliente', $cliente->getId())
                        ->orderBy('cd.id', 'ASC');
                },
                'expanded' => true,
                'multiple' => false
            ])
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('documento');
        $resolver->setDefaults([
            'data_class' => Documento::class,
            'validation_groups' => ['front_carrito_seleccionar_direccion']
        ]);
    }
}
