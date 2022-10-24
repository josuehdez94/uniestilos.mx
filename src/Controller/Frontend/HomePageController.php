<?php

namespace App\Controller\Frontend;

use App\Entity\Categoria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="frontend_home_page")
     */
    public function index()
    {
    
        return $this->render('home_page/paginaPrincipal2.html.twig'); 

    }
    
    /**
     * @Route("/formulario-inicio", name="home_formulario_ingreso", methods={"GET"})
     */
    public function formularioIngreso(){

        return $this->render('home_page/formularioInicio.html.twig', [
            'controller_name' => 'Default',
        ]);
    }

    /**
     * @Route("/inicio", name="home_pagina_inicio", methods={"GET"})
     */
    public function paginaInicio(){

        return $this->render('home_page/formularioInicio.html.twig', [
            'controller_name' => 'Bienvenido a mi sitio',
        ]);
    }

    /**
     * 
     */
    public function navbar(int $max = 3): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categorias = $entityManager->getRepository(Categoria::class)->findAll();
        return $this->render('home_page/navbar.html.twig', [
            'categorias' => $categorias,
            'max' => $max
        ]); 

    }

    /**
     * 
     */
    public function footer(int $max = 3): Response
    {
        return $this->render('home_page/footer.html.twig'); 

    }
}
