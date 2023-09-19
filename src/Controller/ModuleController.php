<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleAddType;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModuleController extends AbstractController
{

    #[Route('/module', name: 'app_module')]
    public function index(ModuleRepository $moduleRepository): Response
    {
        $modules = $moduleRepository->findAll();
        return $this->render('module/index.html.twig', [
            'modules' => $modules,
        ]);
    }


    //Méthode pour créé des module
    #[Route('/module/create_module', name:'create_module')]
    public function createModule(EntityManagerInterface $entityManager, Request $request)
    {   
        $module = new Module();
        $form = $this->createForm(ModuleAddType::class, $module);
        $form->handleRequest($request);
        //si formulaire soumis et valide
        if($form->isSubmitted() && $form->isValid()){
            $module = $form->getData();
            //l'équivalent de prepare en PDO
            $entityManager->persist($module);
            // l'équivalent de execute en PDO
            $entityManager->flush();
            // message de succes
            $this->addFlash('succes-createModule', 'Votre module a bien été créé');

            return $this->redirectToRoute('app_module');
        }
        return $this->render('module/createModule.html.twig',[
            'formCreateModule'=>$form,
        ]);
    }
}
