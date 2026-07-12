<?php

namespace App\Controller;

use App\Entity\MyPassword;
use App\Entity\User;
use App\Enum\AccountActivationStatus;
use App\Form\MyPasswordType;
use App\Form\RenewPasswordType;
use App\Repository\UserRepositoryInterface;
use App\Service\AccountLifecycleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly AccountLifecycleService $accountLifecycle,
    ) {
    }

    /**
     * login.
     */
    #[Route(path: '/login', name: 'login', methods: ['GET', 'POST'])]
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
    #[Route(path: '/user/activate/{token}', name: 'user_activate', methods: ['GET', 'POST'])]
    public function activate(string $token, User $user, Request $request): Response
    {
        if ($request->isMethod('GET')) {
            return $this->render('security/activation.html.twig', [
                'token' => $token,
                'user' => $user,
                'activationStatus' => null,
            ]);
        }

        if (!$this->isCsrfTokenValid('activate-'.$token, (string) $request->request->get('_token'))) {
            throw new BadRequestHttpException('Jeton CSRF invalide.');
        }

        $status = $this->accountLifecycle->activate($user, $token);
        if (AccountActivationStatus::Activated === $status) {
            $this->addFlash('blue', 'Votre compte a été activé');

            return $this->redirectToRoute('login');
        }

        return $this->render('security/activation.html.twig', [
            'token' => $token,
            'user' => $user,
            'activationStatus' => $status->name,
        ]);
    }

    /**
     * Send activate token.
     */
    #[Route(path: 'user/resendactivatetoken/{id}', name: 'user_resend_activation_token', methods: ['POST'])]
    public function resendActivationToken(User $user, Request $request): RedirectResponse
    {
        $csrfTokenId = sprintf('resend-activation-%d', $user->getId() ?? 0);
        if (!$this->isCsrfTokenValid($csrfTokenId, (string) $request->request->get('_token'))) {
            throw new BadRequestHttpException('Jeton CSRF invalide.');
        }

        $this->accountLifecycle->resendActivation($user);
        $this->addFlash('blue', 'Un lien d\'activation vous a été envoyé');

        return $this->redirectToRoute('login');
    }

    /**
     * Allows to initiate the forgotten password method.
     */
    #[Route(path: '/mot-de-passe-oublie', name: 'forgotten_password', methods: ['GET', 'POST'])]
    public function forgetPassword(Request $request, UserRepositoryInterface $userRepository): Response
    {
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('forgotten_password', (string) $request->request->get('_token'))) {
                throw new BadRequestHttpException('Jeton CSRF invalide.');
            }

            $email = (string) $request->request->get('email', '');

            $user = $userRepository->findOneBy(['email' => $email]);
            $this->accountLifecycle->requestPasswordReset($user instanceof User ? $user : null);

            $this->addFlash('blue', 'Si un compte existe avec cette adresse email, un email vous sera envoyé.');

            return $this->redirectToRoute('travel_home');
        }

        return $this->render('user/forgotten_password.html.twig');
    }

    /**
     * Allows you to the reset password.
     */
    #[Route(path: '/reset_password/{token}', name: 'reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(string $token, Request $request, UserRepositoryInterface $userRepository): Response
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
            if (!$this->accountLifecycle->resetPassword($user, $token, $myPassword->getPassword() ?? '')) {
                $this->addFlash('alert', 'Votre token a expiré.');
            } else {
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
    public function newPassword(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(RenewPasswordType::class, []);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();

            $this->accountLifecycle->changePassword($user, is_string($newPassword) ? $newPassword : '');

            $this->addFlash('green accent-3', 'Votre mot de passe a bien été modifié.');

            return $this->redirectToRoute('travel_home');
        }

        return $this->render('user/RenewPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
