<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\RegistrationFormUpdateType;
use App\Security\AppUserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;



class RegistrationController extends AbstractController
{
    #[Route('/admin/membre/register', name: 'new_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppUserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRoles([$form->get('role')->getData()]);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute("list_membres");
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
           
        }

       

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

    }

    #[Route("/list/membres", name:"list_membres")]
    public function listMembre(ManagerRegistry $doctrine) :Response{
        $users = $doctrine->getRepository(User::class)->findAll();
        return $this->render("registration/list-membre.html.twig", ["users" => $users]);
    }

    #[Route("/admin/membre/update/{id}", name:"list_update")]
    public function update($id, ManagerRegistry $doctrine, Request $request){
        $user = $em = $doctrine->getManager()->getRepository(User::class)->find($id);

        if($user === null){
            return $this->redirectToRoute("list_membres");
        }

        $form = $this->createForm(RegistrationFormUpdateType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
                if($form->get('plainPassword')->getData() != ""){
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                                $user,
                                $form->get('plainPassword')->getData()
                            )
                        );
                        
                }
         
                if($form->get("role")->getData() != ""){
                    $user->setRoles([$form->get("role")->getData()]);
                }
            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute("list_membres");
    }

    return $this->render("registration/register.html.twig", ["registrationForm" => $form->createView(), "update" => true]);
}

    #[Route("/admin/membre/delete/{id}", name:"list_delete")]
    public function delete($id, ManagerRegistry $doctrine) :Response{
        $user = $doctrine->getManager()->getRepository(User::class)->find($id);

        if($user === null){
            return $this->redirectToRoute("list_membres");
        }

        $em = $doctrine->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute("list_membres");
    }

    
}
