<?php

namespace App\Controller\Frontend;

use App\Entity\Documento;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Generales\Funciones;

/**
 * @Route("/mi-cuenta")
 */
class ClienteController extends AbstractController
{
    /**
     * @Route("/compras/{user_uid}-{crypt}", name="front_cliente_index_compra", requirements={"crypt": ".+"}, methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager, Request $request, $user_uid, $crypt): Response
    {
        $entityManager->flush();
        $cliente = $entityManager->getRepository(User::class)->findOneBy(['uid' => $user_uid]);
        if(!$cliente){
            throw $this->createNotFoundException('Cliente no encontrado');
        }
        if($cliente->getId() != $this->getUser()->getId()){
            throw $this->createNotFoundException('No puedes acceder a la información de otros clientes.');
        }
        #desencriptar
        $key = explode(',', $crypt)[1];
        $cadena = explode(',', $crypt)[0];
        $decrypt = new Funciones();
        $desencriptar = $decrypt->desencriptar($cadena, $key);
        if ($desencriptar != $cliente->getDecrypt()){
            throw $this->createNotFoundException('No puedes acceder a esta información.');
        }
        $compras = $entityManager->getRepository(Documento::class)->findBy(['tipo' => 'R', 'cliente' => $this->getUser()->getId(), 'finalizado' => true]);
        
        return $this->render('Frontend/Cliente/compras.html.twig', [
            'compras' => $compras,
        ]);
    }

    /**
     * @Route("/compras/detalles/{id}-{crypt}", name="front_cliente_compra_detalles", requirements={"crypt": ".+"}, methods={"GET"})
     */
    public function detallesCompra(EntityManagerInterface $entityManager, $id, $crypt): Response
    {
        $compra = $entityManager->getRepository(Documento::class)->findOneBy(['id' => $id]);
        if(!$compra){
            throw $this->createNotFoundException('Compra no encontrada');
        }
        if($compra->getCliente()->getId() != $this->getUser()->getId()){
            throw $this->createNotFoundException('No puedes acceder a la información de otros clientes.');
        }
        #desencriptar
        $key = explode(',', $crypt)[1];
        $cadena = explode(',', $crypt)[0];
        $decrypt = new Funciones();
        $desencriptar = $decrypt->desencriptar($cadena, $key);
        if ($desencriptar != $compra->getDecrypt()){
            throw $this->createNotFoundException('No puedes acceder a esta información.');
        }

        return $this->render('Frontend/Cliente/detallesCompra.html.twig', [
            'compra' => $compra,
        ]);
    }
}
