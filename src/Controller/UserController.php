<?php

namespace App\Controller;

use App\Dto\EditUserDto;
use App\Dto\RegisterDto;
use App\Entity\User;
use App\Form\EditUserType;
use App\Form\RegisterType;
use App\Service\AddressLookupService;
use App\Service\UserAccountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserAccountService $userAccountService,
    ) {
    }

    #[Route(path: '/register', name: 'register')]
    public function register(Request $request): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('travel_home');
        }

        $form = $this->createForm(RegisterType::class, new RegisterDto());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RegisterDto $dto */
            $dto = $form->getData();
            $this->userAccountService->register($dto);

            $this->addFlash('green accent-3', 'Votre compte a bien été créé. Vous devez l\'activer pour pouvoir vous connecter.');

            return $this->redirectToRoute('login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/profil/edit/', name: 'user_edit')]
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(EditUserType::class, EditUserDto::fromUser($user));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditUserDto $dto */
            $dto = $form->getData();
            $this->userAccountService->updateProfile($user, $dto);

            $this->addFlash('blue darken-1', 'Les données de votre compte ont bien été modifiées');

            return $this->redirectToRoute('user_dashboard');
        }

        return $this->render('user/edituser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/api/address', name: 'api-address', methods: ['GET'])]
    public function api(AddressLookupService $addressLookup, Request $request): JsonResponse
    {
        return $this->json($addressLookup->search($request->query->getString('q')));
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/profil/dashboard', name: 'user_dashboard', methods: ['GET', 'POST'])]
    public function dashboard(): Response
    {
        return $this->render('user/dashboard.html.twig');
    }
}
