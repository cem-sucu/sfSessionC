<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\ModuleSessionType;
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


    #[Route('/session/{id}/add_module', name: 'add_module_session')]
    public function new(Request $request, EntityManagerInterface $entityManager, Session $session, SessionRepository $sessionRepository): Response
    {
        // Utilisez la méthode findModuleDispo pour récupérer les modules disponibles
        $moduleDispo = $sessionRepository->findModuleDispo($session->getId());

        // Créez un nouvel objet Programme
        $programme = new Programme();

        // Créez le formulaire en passant également la liste des modules disponibles
        $form = $this->createForm(ModuleSessionType::class, $programme, [
            'module_dispo' => $moduleDispo,
        ]);

        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $programme = $form->getData();
            $module = $programme->getModule();
            $session->addProgramme($programme->setModule($module));

            // Persistez et flush dans la base de données
            $entityManager->persist($programme);
            $entityManager->flush();

            // Ajouter un message flash
            $this->addFlash('success', 'Module bien ajouté à la session.');

            return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
        }

        return $this->render('session/show.html.twig', [
            'formAddModule' => $form->createView(),
        ]);
    }

    
    // pour afficher la liste des nonInscrits
    // et pour afficher les module dispo mais pas présent dans la session
    // il faut qu'on place la route ID toujours a la fin sinon sa creer des erreur
    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, SessionRepository $sr): Response
    {
        $nonInscrits = $sr->findNonInscrits($session->getId());
        $moduleDispo = $sr->findModuleDispo($session->getId());

        $programme = new Programme();
        $formAddModule = $this->createForm(ModuleSessionType::class, $programme);
    
        return $this->render('session/show.html.twig', [
            'session'=>$session,
            'nonInscrits' => $nonInscrits,
            'moduleDispo' => $moduleDispo,
            'formAddModule' => $formAddModule->createView(),
        ]);
    }



}