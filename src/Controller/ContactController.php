<?php

namespace App\Controller;

use DateTime;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    private $mailer;

    public function __construct( MailerService $mailer ){
       
        $this->mailer = $mailer;
    
    }
    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, MailerService $mailerService)
    {
        //form the contact us
        $contact = new Contact();
        $contact->setSendDate(new \DateTime());
        $form = $this->createForm( ContactType::class, $contact);

        $form->handleRequest( $request );
        
        if( $form->isSubmitted() && $form->isValid() ){

            $em = $this->getDoctrine()->getManager();
            
            $em->persist( $contact );
            $em->flush();
            //email for contact us
            $mailerService->sendContactMessage($contact->getEmail(),$contact);
            //homepage message after the user ask information
            $this->addFlash( 'green accent-3', 'Votre demande a bien été enregistré, Il sera traité dans les plus bref délais' );
            return $this->redirectToRoute( 'home' );
        }
        return $this->render('contact/index.html.twig', [
                'form' => $form->createView(),
        ]);
    }
}
