<?php

namespace App\Controller;

use App\Dto\ContactDto;
use App\Form\ContactType;
use App\Service\ContactService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    /**
     * Contact form.
     */
    #[Route(path: '/contact', name: 'contact')]
    public function contact(Request $request, ContactService $contactService): Response
    {
        $form = $this->createForm(ContactType::class, new ContactDto());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ContactDto $dto */
            $dto = $form->getData();
            $contactService->handle($dto);

            $this->addFlash('green accent-3', 'Votre demande a bien été enregistré, Il sera traité dans les plus bref délais');

            return $this->redirectToRoute('travel_home');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
