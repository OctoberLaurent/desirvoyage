<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use App\Form\RegisterType;
use App\Service\UserService;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class UserController extends AbstractController
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
	 * @Route("/register", name="register")
	 */
	public function register(Request $request): Response
	{
		if ($this->getUser()) {

			return $this->redirectToRoute('travel_home');

		}

		$user = new User();
		$form = $this->createForm(RegisterType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$password = $this->encoder->encodePassword($user, $user->getPassword());
			$user->setPassword($password);
			$user->setRoles(["ROLE_USER"]);

			$this->userService->generateToken($user);


			$em = $this->getDoctrine()->getManager();

			$em->persist($user);
			$em->flush();

			$this->mailer->sendActivationMail($user);

			$this->addFlash('green accent-3', 'Votre compte à bien été créé, activez le pour pouvoir vous connecter');
			return $this->redirectToRoute('login');
		}
		return $this->render('user/register.html.twig', array(
			'form' => $form->createView(),
		));
	}

	/**
	 * @Route("/profil/edit/", name="user_edit")
	 * @IsGranted("ROLE_USER")
	 */
	public function edit(Request $request): Response
	{
		$user = $this->getUser();

		$form = $this->createForm(EditUserType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$em = $this->getDoctrine()->getManager();

			$em->persist($user);
			$em->flush();

			$this->addFlash('blue darken-1', 'Les données de votre compte ont bien été modifiées');
			return $this->redirectToRoute('user_dashboard');
		}
		return $this->render('user/edituser.html.twig', array(
			'form' => $form->createView(),
		));
	}

	/**
	 * @Route("/api/address", name="api-address", methods={"GET"})
	 */
	public function api(HttpClientInterface $httpClient, Request $request)
	{
		$response = $httpClient->request('GET', "https://api-adresse.data.gouv.fr/search/", array(
			'query' => array(
				'q' => $request->query->get('q'),
			)
		));
		return new Response($response->getContent());
	}


	/**
	 * @Route("/profil/dashboard", name="user_dashboard", methods={"GET", "POST"})
	 * @IsGranted("ROLE_USER")
	 */
	public function dashboard()
	{
		return $this->render("user/dashboard.html.twig");
	}
}

