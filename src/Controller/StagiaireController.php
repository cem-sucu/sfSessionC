<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{
    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function index(StagiaireRepository $stagiaireRepository): Response
    {
        $stagiaires = $stagiaireRepository->findAll();
        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires,
        ]);
    }


    // pour le fomurlaaire
    #[Route('/stagiaire/new', name: 'new_stagiaire')]
    //EntityManagerInterface $entityManager on le met en place pour le formulaire et on l'importe
    public function new (Request $request, EntityManagerInterface $entityManager): Response
    {
        $stagiaire = new Stagiaire();
        $form = $this->createForm(StagiaireType::class, $stagiaire);
        $form->handleRequest($request);
        //si formulaire soumis est valide
        if ($form->isSubmitted() && $form->isValid()) {
            $stagiaire = $form->getData();
            //l'équivalent de prepare PDO
            $entityManager->persist($stagiaire);
            // l'équivalent du execute en PDO
            $entityManager->flush();
            // Ajouter un message flash
            $this->addFlash('success', 'Stagiaire bien ajouté');
            return $this->redirectToRoute('app_stagiaire');
        }

        return $this->render('stagiaire/new.html.twig', [
            'formAddStagiaire'=>$form,
        ]);
    }

    //il faut qu'on place la route ID toujours a la fin sinon sa creer des erreur
    #[Route('/stagiaire/{id}', name: 'show_stagiaire')]
    public function show(Stagiaire $stagiaire): Response
    {
        return $this->render('stagiaire/show.html.twig', [
            'stagiaire' => $stagiaire,
        ]);
    }

 

}
