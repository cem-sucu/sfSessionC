<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\SessionAddType;
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

    //supprimer des module d'une session défini
    #[Route('/session/{id}/remove_module/{programme}', name: 'remove_module_session')]
    public function removeModule( EntityManagerInterface $entityManager, Session $session, Programme $programme)
    {
        if ($session && $programme){
            $session->removeProgramme($programme);
            $entityManager->persist($session);
            $entityManager->flush();

            $this->addFlash('success-module', 'Module enlevé de la session.');
        } else {
            $this->addFlash('error-module', 'Erreur lors de la suppresion du Module de la session.');
        }
        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    }

    //methode pour ajouter une nouvelle session
    #[Route('/session/add_session', name:'add_session')]
    public function addSession(EntityManagerInterface $entityManager, Request $request )
    {
        $session = new Session();
        $form = $this->createForm(SessionAddType::class, $session);
        $form->handleRequest($request);
        //si form est soumis
        if($form->isSubmitted() && $form->isValid()){
            $session = $form->getData();
            //l'équivalent de prepare en PDO
            $entityManager->persist($session);
            // l'équivalent de execute en PDO
            $entityManager->flush();
            //message de succes
            $this->addFlash('succes-addSession', 'une nouvelle session a bien été ajouté');
            return $this->redirectToRoute('app_session');
        }
        return $this->render('session/addSession.html.twig', [
            'formAddSession'=>$form,
        ]);
    }

    //ajouter des module dans une session
    #[Route('/session/{id}/add_module', name: 'add_module_session')]
    public function new(Request $request, EntityManagerInterface $entityManager, Session $session)
    {
       $form = $this->createForm(ModuleSessionType::class);

        $form->handleRequest($request);
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // var_dump("form ok");die;
            $programme = $form->getData();
            $session->addProgramme($programme);
             //l'équivalent de prepare PDO
            $entityManager->persist($programme);
            // l'équivalent du execute en PDO
            $entityManager->flush();
            // Ajouter un message flash
            $this->addFlash('success-module', 'Module bien ajouté à la session.');

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