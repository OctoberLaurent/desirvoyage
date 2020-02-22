<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\MyPassword;
use App\Form\MyPasswordType;
use App\Service\UserService;
use App\Service\MailerService;
use App\Form\RenewPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SecurityController extends AbstractController
{
	private $encoder;
	private $userService;
	private $mailer;
	private $urlGenerator;

	public function __construct(UserPasswordEncoderInterface $encoder, MailerService $mailer, UserService $userService, UrlGeneratorInterface $urlGenerator)
	{
		$this->encoder = $encoder;
		$this->mailer = $mailer;
		$this->userService = $userService;
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * login
	 * 
	 * @Route("/login", name="login")
	 */
	public function login(AuthenticationUtils $authenticationUtils): Response
	{
		if ($this->getUser()) {
			$this->addFlash('blue', 'Vous êtes déja connecté(e)');
			return $this->redirectToRoute('user_dashboard');
		}

		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();
		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		if ($error) {
			$this->addFlash('red', $error->getmessage());
		}
		return $this->render('security/login.html.twig', array(
			'last_username' => $lastUsername,
			'error' => $error));
	}

	/**
	 * logout
	 * 
	 * @Route("/logout", name="logout")
	 */
	public function logout()
	{
		//Je n'affiche rien dans la fonction. Tout est fait automatiquement
	}

	/**
	 * Activate account
	 *
	 * @Route("/user/activate/{token}", name="user_activate")
	 */
	public function activate($token, User $user)
	{
		if (!$user->getEnabled()) {
			if ($user->getTokenExpire() > new \DateTime()) {
				// set enable true and token null if valid condition
				$user->setEnabled(true);
				$this->userService->resetToken($user);
				// database entry
				$em = $this->getDoctrine()->getManager();
				$em->flush($user);
				// add message if account is activate
				$this->addFlash(
					'blue',
					'Votre compte a été activé');
			} else {
				// add message if date is expired
				$url = $this->urlGenerator->generate('user_resendactivatetoken', ['id' => $user->getId()], UrlGenerator::ABSOLUTE_URL);

				$this->addFlash(
					'red',
					'Ce lien a expiré <a href="' . $url . '"> Renvoyer le mail d\'activation </a>');
			}
		}
		// redirect to login route
		return $this->redirectToRoute('login');
	}

	/**
	 * Send activate token
	 *
	 * @Route("user/resendactivatetoken/{id}", name="user_resendactivatetoken")
	 */
	public function resendactivatetoken(User $user)
	{

		if (!$user->getEnabled()) {
			// generate token and expire date
			$this->userService->generateToken($user);
			$em = $this->getDoctrine()->getManager();
			$em->flush($user);
			// resend a activation token
			$this->mailer->sendActivationMail($user);
			// message if link is send.
			$this->addFlash(
				'blue',
				'Un lien d\'activation vous a été envoyé');
			// redirect to login route
			return $this->redirectToRoute('login');
		}
	}

	/**
	 * Allows to initiate the forgotten password method
	 *
	 * @Route("/mot-de-passe-oublie",name="forgotten_password")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function forgetPassword(Request $request)
	{
		if ($request->isMethod('POST')) {
			$email = $request->request->get('email');

			$entityManager = $this->getDoctrine()->getManager();
			$user = $entityManager->getRepository(User::class)->findOneByEmail($email);

			if ($user) {
				$this->userService->generateToken($user);
				$entityManager->flush();

				$this->mailer->sendResetPassword($user);
			}

			$this->addFlash('blue', 'Si un compte existe avec cette adresse email, un email vous sera envoyé.');
			return $this->redirectToRoute('home');
		}
		return $this->render('user/forgotten_password.html.twig');
	}

	/**
	 * Permet de réintialiser le mot de passe
	 *
	 * @Route("/reset_password/{token}", name="reset_password")
	 *
	 * @param string $token
	 * @param Request $request
	 * @return Response
	 */
	public function resetPassword(string $token, Request $request): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$user = $entityManager->getRepository(User::class)->findOneByToken($token);

		if ($user == null) {
			// A rediriger vers la 404
			return $this->redirectToRoute('home');
		}

		$myPassword = new MyPassword;

		$form = $this->createForm(MyPasswordType::class, $myPassword);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			if ($user->getExpiredToken() < new \DateTime()) {
				$this->addFlash('alert', 'Votre token a expiré.');
			} else {
				$user->setPassword($this->encoder->encodePassword($user, $myPassword->getPassword()));
				$this->userService->resetToken($user);
				$entityManager->flush();

				$this->addFlash('green accent-3', 'Le mot de passe a bien été modifié.');
			}
			return $this->redirectToRoute('home');
		}

		return $this->render('security/reset_password.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * Permet de changer de mot de passe
	 *
	 * @Route("/newpassword", name="new_password", methods={"GET", "POST"})
	 *
	 * @param Request $request
	 * @return RedirectResponse|Response
	 */
	public function newPassword(Request $request): Response
	{
		$user = $this->getUser();

		$form = $this->createForm(RenewPasswordType::class, []);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$newPassword = $form["password"]->getData("password");

			$user->setPassword($this->encoder->encodePassword($user, $newPassword));

			$this
				->getDoctrine()
				->getManager()
				->flush();

			$this->addFlash('green accent-3', "Votre mot de passe a bien été modifié.");

			return $this->redirectToRoute("home");
		}

		return $this->render("user/RenewPassword.html.twig", [
			"form" => $form->createView(),
		]);
	}

}
