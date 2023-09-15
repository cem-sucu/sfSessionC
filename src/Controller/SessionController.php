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

    // #[Route('/session/{id}', name: 'show_session')]
    // public function desinscrireStagiaire(Session $session, Stagiaire $stagiaire, EntityManagerInterface $entityManager): Response
    // {
    //     // comme pour l'ajout je vérifie si le stagiaire est inscrit dans la session
    //     if ($session->getStagiaires()->contains($stagiaire)) {
    //         //sil est ionscrti dans la session je le supprimer/désinscrit
    //         $session->removeStagiaire($stagiaire);
    //         $entityManager->flush();
    //         $this->addFlash('success-message', 'Vous avez bien déinscrit le/la stagiaire.');
    //     } else {
            
    //     }

    //     return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    // }


    // pour afficher la liste des nonInscrits
    // et pour afficher les module dispo mais pas présent dans la session
    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, Request $request, EntityManagerInterface $entityManager, SessionRepository $sessionRepository): Response
    {
        // Vérifie si la requête est de type POST lorsque le formulaire est soumis
        if ($request->isMethod('POST')) {
            // Action d'ajout ou de désinscription
            $action = $request->request->get('action'); 
            $stagiaireId = $request->request->get('stagiaire_id');
    
            // si l'action est désinscrire 
            if ($action === 'inscrire') {
                // on recherche un stagiaire selon son id
                $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($stagiaireId);
    
                // si stagiaire existe et n'est pas insccrirt 
                if ($stagiaire && !$session->getStagiaires()->contains($stagiaire)) {
                    //on ajoute le stagiaire a la session
                    $session->addStagiaire($stagiaire);
                    // on enregistre dans la bdd 
                    // on tire la chasse d'eau
                    $entityManager->flush();
                    $this->addFlash('success-message', 'Stagiaire ajouté à la session.');
                } else {
                    $this->addFlash('error-message', 'Ce stagiaire est déjà inscrit à la session ou n\'existe pas.');
                }
                //sinon si action = descincrire
            } elseif ($action === 'desinscrire') {
                // on recherche un stagiaire selon son id
                $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($stagiaireId);
    
                // si stagiaire est inscrit dans la session
                if ($session->getStagiaires()->contains($stagiaire)) {
                    // on retire le stagiaire inscrit
                    $session->removeStagiaire($stagiaire);
                    // et on enregistre aussi les donné en bdd
                    // on tire la chasse d'eau
                    $entityManager->flush();
                    $this->addFlash('success-message', 'Stagiaire désinscrit de la session.');
                } else {
                    $this->addFlash('error-message', 'Ce stagiaire n\'est pas inscrit à la session.');
                }
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