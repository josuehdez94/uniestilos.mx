<?php

namespace App\Controller\Frontend;

use App\Entity\Categoria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoriaRepository;

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
    public function navbar(EntityManagerInterface $entityManager, CategoriaRepository $categoriaRepository, int $max = 3): Response
    {
        $categorias = $categoriaRepository->findAll();
        $categoriasHombre = $categoriaRepository->getCategoriasPorClasificacion('Hombre'); 
        $categoriasMujer = $categoriaRepository->getCategoriasPorClasificacion('Mujer'); 
        $categoriasNiño = $categoriaRepository->getCategoriasPorClasificacion('Niño'); 
        $categoriasNiña = $categoriaRepository->getCategoriasPorClasificacion('Niña'); 
        $categoriasUnisex = $categoriaRepository->getCategoriasPorClasificacion('Unisex'); 
        return $this->render('home_page/navbar.html.twig', [
            'categorias' => $categorias,
            'categoriasHombre' => $categoriasHombre,
            'categoriasMujer' => $categoriasMujer,
            'categoriasNiño' => $categoriasNiño,
            'categoriasNiña' => $categoriasNiña,
            'categoriasUnisex' => $categoriasUnisex,
            'max' => $max
        ]); 

    }

    /**
     * 
     */
    public function footer(EntityManagerInterface $entityManager, CategoriaRepository $categoriaRepository, int $max = 3): Response
    {
        $categoriasHombre = $categoriaRepository->getCategoriasPorClasificacion('Hombre'); 
        $categoriasMujer = $categoriaRepository->getCategoriasPorClasificacion('Mujer');
        return $this->render('home_page/footer.html.twig', [
            'categoriasHombre' => $categoriasHombre,
            'categoriasMujer' => $categoriasMujer
        ]); 

    }
}
