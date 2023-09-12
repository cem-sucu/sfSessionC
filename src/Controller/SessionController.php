<?php

namespace App\Controller;

use App\Entity\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    // EntityManagerInterface on l'importe
    public function index(EntityManagerInterface $entityManager): Response
    {
        // j'oublie pas de importer Session : App\Entity
        $sessions =$entityManager->getRepository(Session::class)->findAll();
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }
}
