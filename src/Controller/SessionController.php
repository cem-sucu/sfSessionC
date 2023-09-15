<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    // SessionRepository on l'importe
    public function index(SessionRepository $sessionRepository): Response   
    {
        $sessions = $sessionRepository->findAll();
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }

    // pour afficher la liste des nonInscrits
    // et pour afficher les module dispo mais pas présent dans la session
    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, SessionRepository $sr): Response
    {
        $nonInscrits = $sr->findNonInscrits($session->getId());
        $moduleDispo = $sr->findModuleDispo($session->getId());
        return $this->render('session/show.html.twig', [
            'session'=>$session,
            'nonInscrits' => $nonInscrits,
            'moduleDispo' => $moduleDispo,
        ]);
    }
}

