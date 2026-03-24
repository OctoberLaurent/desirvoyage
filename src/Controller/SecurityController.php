<?php

namespace App\Controller;

use App\Entity\MyPassword;
use App\Entity\User;
use App\Form\MyPasswordType;
use App\Form\RenewPasswordType;
use App\Service\MailerService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $encoder;
    private $userService;
    private $mailer;
    private $urlGenerator;

    public function __construct(UserPasswordHasherInterface $encoder, MailerService $mailer, UserService $userService, UrlGeneratorInterface $urlGenerator)
    {
        $this->encoder = $encoder;
        $this->mailer = $mailer;
        $this->userService = $userService;
        $this->urlGenerator = $urlGenerator;
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

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
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
    }

    /**
     * Activate account.
     */
    #[Route(path: '/user/activate/{token}', name: 'user_activate')]
    public function activate($token, User $user, \Doctrine\ORM\EntityManagerInterface $em): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        if (!$user->getEnabled()) {
            if ($user->getTokenExpire() > new \DateTime()) {
                // set enable true and token null if valid condition
                $user->setEnabled(true);
                $this->userService->resetToken($user);
                // database entry
                $em->flush();
                // add message if account is activate
                $this->addFlash(
                    'blue',
                    'Votre compte a été activé');
            } else {
                // add message if date is expired
                $url = $this->urlGenerator->generate('user_resendactivatetoken', ['id' => $user->getId()], UrlGenerator::ABSOLUTE_URL);

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
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|null
     */
    #[Route(path: 'user/resendactivatetoken/{id}', name: 'user_resendactivatetoken')]
    public function resendactivatetoken(User $user, \Doctrine\ORM\EntityManagerInterface $em)
    {
        if (!$user->getEnabled()) {
            // generate token and expire date
            $this->userService->generateToken($user);
            $em->flush();
            // resend a activation token
            $this->mailer->sendActivationMail($user);
            // message if link is send.
            $this->addFlash(
                'blue',
                'Un lien d\'activation vous a été envoyé');

            // redirect to login route
            return $this->redirectToRoute('login');
        }

        return $this->redirectToRoute('login');
    }

    /**
     * Allows to initiate the forgotten password method.
     *
     * @return Response
     */
    #[Route(path: '/mot-de-passe-oublie', name: 'forgotten_password')]
    public function forgetPassword(Request $request, \Doctrine\ORM\EntityManagerInterface $entityManager)
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

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
    public function resetPassword(string $token, Request $request, \Doctrine\ORM\EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

        if (null === $user) {
            // To redirect to the 404
            return $this->redirectToRoute('travel_home');
        }

        $myPassword = new MyPassword();

        $form = $this->createForm(MyPasswordType::class, $myPassword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getTokenExpire() < new \DateTime()) {
                $this->addFlash('alert', 'Votre token a expiré.');
            } else {
                $user->setPassword($this->encoder->hashPassword($user, $myPassword->getPassword()));
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
    public function newPassword(Request $request, \Doctrine\ORM\EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(RenewPasswordType::class, []);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();

            $user->setPassword($this->encoder->hashPassword($user, $newPassword));

            $em->flush();

            $this->addFlash('green accent-3', 'Votre mot de passe a bien été modifié.');

            return $this->redirectToRoute('travel_home');
        }

        return $this->render('user/RenewPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
