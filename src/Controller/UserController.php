<?php

namespace App\Controller;

use App\Dto\RegisterDto;
use App\Entity\User;
use App\Form\EditUserType;
use App\Form\RegisterType;
use App\Service\MailerService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class UserController extends AbstractController
{
    public function __construct(
        private readonly MailerService $mailer,
        private readonly UserService $userService,
    ) {
    }

    #[Route(path: '/register', name: 'register')]
    public function register(Request $request, EntityManagerInterface $em): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('travel_home');
        }

        $form = $this->createForm(RegisterType::class, new RegisterDto());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RegisterDto $dto */
            $dto = $form->getData();
            $user = $this->instantiateUser($dto);

            $em->persist($user);
            $em->flush();

            $this->mailer->sendActivationMail($user);

            $this->addFlash('green accent-3', 'Votre compte a bien été créé. Vous devez l\'activer pour pouvoir vous connecter.');

            return $this->redirectToRoute('login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function instantiateUser(RegisterDto $dto): User
    {
        $user = new User();
        $user->setLastname($dto->lastname ?? '');
        $user->setFirstname($dto->firstname ?? '');
        $user->setBirthday($dto->birthday ?? new \DateTime());
        $user->setAddress($dto->address ?? '');
        $user->setAdditionalAddress($dto->additionalAddress);
        $user->setPostalCode($dto->postalCode ?? '');
        $user->setCity($dto->city ?? '');
        $user->setCountry($dto->country ?? '');
        $user->setPhone($dto->phone ?? '');
        $user->setEmail($dto->email ?? '');
        $this->userService->setPassword($user, $dto->password ?? '');
        $user->setRoles(['ROLE_USER']);
        $this->userService->generateToken($user);

        return $user;
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/profil/edit/', name: 'user_edit')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(EditUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('blue darken-1', 'Les données de votre compte ont bien été modifiées');

            return $this->redirectToRoute('user_dashboard');
        }

        return $this->render('user/edituser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/api/address', name: 'api-address', methods: ['GET'])]
    public function api(HttpClientInterface $httpClient, Request $request): Response
    {
        $response = $httpClient->request('GET', 'https://api-adresse.data.gouv.fr/search/', [
            'query' => [
                'q' => $request->query->get('q'),
            ],
        ]);

        return new Response($response->getContent());
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/profil/dashboard', name: 'user_dashboard', methods: ['GET', 'POST'])]
    public function dashboard(): Response
    {
        return $this->render('user/dashboard.html.twig');
    }
}
