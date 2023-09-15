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

    // pour afficher la liste des nonInscrits
    // et pour afficher les module dispo mais pas présent dans la session
    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, Request $request, EntityManagerInterface $entityManager, SessionRepository $sessionRepository): Response
    {
        // je vérifie si la méthode est de type post
        if ($request->isMethod('POST')) {
            //je recupere le ID du stagiaire
            $stagiaireId = $request->request->get('stagiaire_id');
            // on vérifie que le stagiaire n'est pas déjà inscrit dans la session
            $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($stagiaireId);
            //si stagiaire existe et pas encore inscrit donc visible dans la liste des non inscrit
            if ($stagiaire && !$session->getStagiaires()->contains($stagiaire)) {
                //alors on ajoute le stagiaire a la session
                $session->addStagiaire($stagiaire);
                $entityManager->flush();
                $this->addFlash('success-message', 'Stagiaire ajouté');
            } else {
                $this->addFlash('error-message', 'Ce stagiaire est déjà inscrit à la session ou n\'existe pas.');
            }
        }
    
    $nonInscrits = $sessionRepository->findNonInscrits($session->getId());
    $moduleDispo = $sessionRepository->findModuleDispo($session->getId());
    
    return $this->render('session/show.html.twig', [
        'session' => $session,
        'nonInscrits' => $nonInscrits,
        'moduleDispo' => $moduleDispo,
    ]);
    }


}