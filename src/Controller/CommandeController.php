<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Vehicule;
use App\Form\CommandeType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{

    #[Route("/liste/commande", name: "list_commande")]
    public function liste(ManagerRegistry $doctrine)
    {
        $commandes = $doctrine->getRepository(Commande::class)->findAll();
        return $this->render("commande/list-commandes.html.twig", compact("commandes"));
    }


    /**
     * Il est possible d'associer plusieurs routes à une seule méthode
     * Pratique pour le new et update qui ont presque le même code
     */

    #[Route("/new/commande", name: "form_commande")]
    #[Route("/update/{id}", name: "commande_update")]
    public function new_commande(Request $request, EntityManagerInterface $em, Commande $commande = null): Response
    {

        // Si on appelle new alors la commande est null puisque pas d'id, donc nouveau formulaire mais si on appelle {id} -> alors formulaire prè rempli
        if($commande == null){ 
            $commande = new Commande();
            $commande->setDtHeureDepart(new DateTime())
                    ->setDtHeureFin(NEW DateTime());
        }
       
        $form = $this->createForm(CommandeType::class, $commande);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupérer dt_debut / dt_fin 
            $dt_debut = $form->get("dt_heure_depart")->getData();
            $dt_fin = $form->get("dt_heure_fin")->getData();
            $interval = $dt_debut->diff($dt_fin);
            $interval->format("%r%a");
            $nbJours = $interval->days;

            if($nbJours < 1){
                $this->addFlash("message", "Une réservation doit durer au minimum 24h !");
            }

            // Vérification de la disponibilité des véhicule

            $listeVehiculeLoue = $em->getRepository(Commande::class)->listeVehiculeLoue($dt_debut, $dt_fin);
            $vehicule = $form->get("vehicule")->getData();
            if(in_array($vehicule->getId(),  $listeVehiculeLoue)){
                $listeVehiculeDispo = $em->getRepository(Vehicule::class)->findByVehiculeDisponibles($listeVehiculeLoue);
                $this->addFlash("message", "Le véhicule demandé est déjà reservé pour cette période !");
                $this->addFlash("vehicules", ["disponibles" => $listeVehiculeDispo]);
            }
           

            // Récupérer le prix selon le véhicule choisi 
            
            if(!in_array($vehicule->getId(), $listeVehiculeLoue) && $nbJours >= 1){

                $prixJournalier = $vehicule->getPrixJournalier();

                // Multiplication = nb_jour * prix_journalier
                $commande->setPrixTotal($nbJours * $prixJournalier);
    
                $em->persist($commande);
                $em->flush();
                return $this->redirectToRoute("list_commande");
            }
           
        }
        return $this->render("commande/form-commande.html.twig", ["form" => $form->createView(),
                                                                    "id" => $commande->getId()]);
    }

    
    // ParamConverter
    #[Route("/delete/{id}", name: "commande_delete")]
    public function delete(Commande $commande, EntityManagerInterface $em): Response{

        

        if ($commande === null) {
            return $this->redirectToRoute("list_commande");
        }

        if ($commande) {
            $em->remove($commande);
            $em->flush();
            return $this->redirectToRoute("list_commande");
        }
    }
}
