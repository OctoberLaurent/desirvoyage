<?php

namespace App\Controller;

use App\Entity\MyPassword;
use App\Entity\User;
use App\Form\MyPasswordType;
use App\Form\RenewPasswordType;
use App\Repository\UserRepositoryInterface;
use App\Service\MailerService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly MailerService $mailer,
        private readonly UserService $userService,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * login.
     */
    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if (null !== $this->getUser()) {
            $this->addFlash('blue', 'Vous êtes déja connecté(e)');

            return $this->redirectToRoute('user_dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if (null !== $error) {
            $this->addFlash('red', $error->getMessage());
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error]);
    }

    /**
     * logout.
     */
    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Activate account.
     */
    #[Route(path: '/user/activate/{token}', name: 'user_activate')]
    public function activate(string $token, User $user, EntityManagerInterface $em): RedirectResponse
    {
        if (true !== $user->getEnabled()) {
            $tokenExpire = $user->getTokenExpire();
            if (null !== $tokenExpire && $tokenExpire > new \DateTime()) {
                $user->setEnabled(true);
                $this->userService->resetToken($user);
                $em->flush();
                $this->addFlash(
                    'blue',
                    'Votre compte a été activé');
            } else {
                $url = $this->urlGenerator->generate('user_resend_activation_token', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

                $this->addFlash(
                    'red',
                    'Ce lien a expiré <a href="'.$url.'"> Renvoyer le mail d\'activation </a>');
            }
        }

        // redirect to login route
        return $this->redirectToRoute('login');
    }

    /**
     * Send activate token.
     */
    #[Route(path: 'user/resendactivatetoken/{id}', name: 'user_resend_activation_token')]
    public function resendActivationToken(User $user, EntityManagerInterface $em): RedirectResponse
    {
        if (true !== $user->getEnabled()) {
            // generate token and expire date
            $this->userService->generateToken($user);
            $em->flush();
            // resend a activation token
            $this->mailer->sendActivationMail($user);
            // message if link is send.
            $this->addFlash(
                'blue',
                'Un lien d\'activation vous a été envoyé');
        }

        return $this->redirectToRoute('login');
    }

    /**
     * Allows to initiate the forgotten password method.
     */
    #[Route(path: '/mot-de-passe-oublie', name: 'forgotten_password')]
    public function forgetPassword(Request $request, UserRepositoryInterface $userRepository, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $email = (string) $request->request->get('email', '');

            $user = $userRepository->findOneBy(['email' => $email]);

            if (null !== $user) {
                $this->userService->generateToken($user);
                $entityManager->flush();

                $this->mailer->sendResetPassword($user);
            }

            $this->addFlash('blue', 'Si un compte existe avec cette adresse email, un email vous sera envoyé.');

            return $this->redirectToRoute('travel_home');
        }

        return $this->render('user/forgotten_password.html.twig');
    }

    /**
     * Allows you to the reset password.
     */
    #[Route(path: '/reset_password/{token}', name: 'reset_password')]
    public function resetPassword(string $token, Request $request, UserRepositoryInterface $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy(['token' => $token]);

        if (null === $user) {
            // To redirect to the 404
            return $this->redirectToRoute('travel_home');
        }

        $myPassword = new MyPassword();

        $form = $this->createForm(MyPasswordType::class, $myPassword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tokenExpire = $user->getTokenExpire();
            if (null !== $tokenExpire && $tokenExpire < new \DateTime()) {
                $this->addFlash('alert', 'Votre token a expiré.');
            } else {
                $this->userService->setPassword($user, $myPassword->getPassword() ?? '');
                $this->userService->resetToken($user);
                $entityManager->flush();

                $this->addFlash('green accent-3', 'Le mot de passe a bien été modifié.');
            }

            return $this->redirectToRoute('travel_home');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Allows you to change your password.
     */
    #[Route(path: '/newpassword', name: 'new_password', methods: ['GET', 'POST'])]
    public function newPassword(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(RenewPasswordType::class, []);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();

            $this->userService->setPassword($user, is_string($newPassword) ? $newPassword : '');

            $em->flush();

            $this->addFlash('green accent-3', 'Votre mot de passe a bien été modifié.');

            return $this->redirectToRoute('travel_home');
        }

        return $this->render('user/RenewPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
