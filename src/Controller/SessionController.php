<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    //la methode pour ajouter un stagiaire a une session
    #[Route('/session/add/{session}/{stagiaire}', name: 'add_stagiaire')]
    public function addStagiaire(EntityManagerInterface $entityManager, Session $session, Stagiaire $stagiaire)
    {
        if ($stagiaire && $session) {
            $session->addStagiaire($stagiaire);
            $entityManager->persist($session);
            $entityManager->flush();
    
            $this->addFlash('success-message', 'Stagiaire ajouté à la session.');
        } else {
            $this->addFlash('error-message', 'Erreur lors de l\'ajout du stagiaire à la session.');
        }
    
        return $this->redirectToRoute('show_session', ['id' =>$session->getId()]);
    }

    //la methode pour enlever un stagiaire/désinscrire d'une seesion
    #[Route('/session/remove/{session}/{stagiaire}', name: 'remove_stagiaire')]
    public function removeStagiaire(EntityManagerInterface $entityManager, Session $session, Stagiaire $stagiaire)
    {
        if ($stagiaire && $session) {
            $session->removeStagiaire($stagiaire);
            $entityManager->persist($session);
            $entityManager->flush();

            $this->addFlash('success-message', 'Stagiaire enlevé de la session.');
        } else {
            $this->addFlash('error-message', 'Erreur lors de la désinscription du stagiaire de la session.');
        }
    
        return $this->redirectToRoute('show_session', ['id' =>$session->getId()]);
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