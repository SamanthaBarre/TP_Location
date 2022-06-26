<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Services\ImageService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route("/vehicule")]
class VehiculeController extends AbstractController{

    private $imgService; // Variable qui va contenir la class ImageService 

    // public function __construct(EntityManagerInterface $em, ImageService $imgService)
    // {
    //     $this->imgService = $imgService;
    // }

    #[Route("/", name:"home_vehicule")]
    public function index(ManagerRegistry $doctrine) :Response{

        $vehicules = $doctrine->getRepository(Vehicule::class)->findAll();
        return $this->render("vehicule/list-vehicule.html.twig", ["vehicules" => $vehicules]);
    }

    #[Route("/new", name:"form_vehicule")] 
    public function new(Request $request, EntityManagerInterface $em) :Response{ // On rajoute  la class ImageServices qu'on stock dans une variable $imgService

        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $file = $request->files->get("vehicule")["photo"];
            $upload = $this->getParameter("upload_directory");
            $photo = md5(uniqid()) . "." . $file->guessExtension();
            $file->move($upload, $photo);
            $vehicule->setPhoto($photo);

            $em->persist($vehicule);
            $em->flush();
            return $this->redirectToRoute("home_vehicule");
        }
        return $this->render("vehicule/form-vehicule.html.twig", ["form" => $form->createView()]);
    }


    #[Route("/update/{id}", name:"update_vehicule")]
    public function update($id, ManagerRegistry $doctrine, Request $request) :Response{
        $vehicule = $doctrine->getManager()->getRepository(Vehicule::class)->find($id);

        if($vehicule === null){
            return $this->redirectToRoute("home_vehicule");
        }

        $form = $this->createForm(VehiculeType::class, $vehicule);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $em->persist($vehicule);
            $em->flush();
            return $this->redirectToRoute("home_vehicule");
    }
        return $this->render("vehicule/form-vehicule.html.twig", ["form" => $form->createView(), "update" => true]);
}

        #[Route("/delete/{id}", name:"delete_vehicule")]
        public function delete($id, ManagerRegistry $doctrine) :Response{

            $vehicule = $doctrine->getManager()->getRepository(Vehicule::class)->find($id);

            if($vehicule === null){
                return $this->redirectToRoute("home_vehicule");
            }

            if($vehicule){
                // Suppression de la photo dans le dossier upload
                $upload = $this->getParameter("upload_directory");
                $photo = $vehicule->getPhoto();
                unlink($upload ."/" . $photo);
                
            $em = $doctrine->getManager();
            $em->remove($vehicule);
            $em->flush();
            return $this->redirectToRoute("home_vehicule");
            }

        }
}