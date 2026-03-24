<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    /**
     * Contact form.
     */
    #[Route(path: '/contact', name: 'contact')]
    public function contact(Request $request, MailerService $mailerService, \Doctrine\ORM\EntityManagerInterface $em): \Symfony\Component\HttpFoundation\Response
    {
        // form the contact us
        $contact = new Contact();
        $contact->setSendDate(new \DateTime());
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($contact);
            $em->flush();
            // email for contact us
            $mailerService->sendContactMessage((string) $contact->getEmail());
            // homepage message after the user ask information
            $this->addFlash('green accent-3', 'Votre demande a bien été enregistré, Il sera traité dans les plus bref délais');

            return $this->redirectToRoute('travel_home');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
