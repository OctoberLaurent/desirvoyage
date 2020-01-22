<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
            $this->addFlash('info', 'Vous êtes déja connecté(e)');
            return $this->redirectToRoute('home');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        if ($error){
             $this->addFlash('danger', $error->getmessage() );
        }
        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername, 
            'error' => $error));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        //Je n'affiche rien dans la fonction. Tout est fait automatiquement
    }
}
