<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MetodosPagoController extends AbstractController
{
    /**
     * @Route("/metodos/pago", name="metodos_pago")
     */
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $articulo = $entityManager->getRepository(\App\Entity\Articulo::class)->findOneBy(['id' => 1]);
        $articulo->setPrecio1(500);
        $entityManager->flush();
        return new Response('success');
    }
}
