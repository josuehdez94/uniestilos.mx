<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\LogRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/backend/log-sistema")
 */
class LogSistemaController extends AbstractController
{
    /**
     * @Route("/", name="backend_log_sistema_index", methods={"GET"})
     */
    public function index(LogRepository $logRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $errores = $paginator->paginate(
            $logRepository->findBy([],['createdAt' => 'DESC']),
            $request->query->getInt('pagina', 1),
            70
        );
        return $this->render('backend/LogSistema/index.html.twig', [
            'logs' => $errores
        ]);
    }
}
