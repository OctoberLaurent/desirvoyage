<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use App\Form\RegisterType;
use App\Service\MailerService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserController extends AbstractController
{
    private $encoder;
    private $userService;
    private $mailer;

    public function __construct(UserPasswordHasherInterface $encoder, MailerService $mailer, UserService $userService)
    {
        $this->encoder = $encoder;
        $this->mailer = $mailer;
        $this->userService = $userService;
    }

    #[Route(path: '/register', name: 'register')]
    public function register(Request $request, \Doctrine\ORM\EntityManagerInterface $em): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('travel_home');
        }

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);

            $this->userService->generateToken($user);

            $em->persist($user);
            $em->flush();

            $this->mailer->sendActivationMail($user);

            $this->addFlash('green accent-3', 'Votre compte à bien été créé, activez le pour pouvoir vous connecter');

            return $this->redirectToRoute('login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/profil/edit/', name: 'user_edit')]
    public function edit(Request $request, \Doctrine\ORM\EntityManagerInterface $em): Response
    {
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
