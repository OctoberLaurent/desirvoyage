<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm( ContactType::class, $contact);

        $form->handleRequest( $request );
        
        if( $form->isSubmitted() && $form->isValid() ){

            $em = $this->getDoctrine()->getManager();
            
            $em->persist( $contact );
            $em->flush();

           

            $this->addFlash( 'green accent-3', 'Votre demande a bien été enregistré, Il sera traité dans les plus bref délais' );
            return $this->redirectToRoute( 'home' );
        }
        return $this->render('contact/index.html.twig', [
                'form' => $form->createView(),
        ]);
    }
}
