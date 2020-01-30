<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Service\UserService;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserController extends AbstractController
{
    private $encoder;
    private $userService;
    private $mailer;
    private $urlGenerator;
   
    public function __construct( UserPasswordEncoderInterface $encoder, MailerService $mailer, UserService $userService, UrlGeneratorInterface $urlGenerator ){
        $this->encoder = $encoder;
        $this->mailer = $mailer;
        $this->userService = $userService;
        $this->urlGenerator = $urlGenerator;
    }
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request):Response
    {
        if( $this->getUser() ){
            return $this->redirectToRoute('home');
            
        }

        $user = new User();
        $form = $this->createForm( RegisterType::class, $user );

        $form->handleRequest( $request );
        
        if( $form->isSubmitted() && $form->isValid() ){
            $password = $this->encoder->encodePassword( $user, $user->getPassword() );
            $user->setPassword( $password );
            $user->setRoles( ["ROLE_USER"] );

            $this->userService->generateToken( $user );
            

            $em = $this->getDoctrine()->getManager();
            
            $em->persist( $user );
            $em->flush();

            $this->mailer->sendActivationMail( $user );

            $this->addFlash( 'green accent-3', 'Votre compte à bien été créé, activez le pour pouvoir vous connecter' );
            return $this->redirectToRoute( 'login' );
        }
        return $this->render('user/register.html.twig', array(
            'form' => $form->createView(),
        ));  
    }

    /**
     * @Route("/api/address", name="api-address", methods={"GET"})
     */
    public function api(HttpClientInterface $httpClient, Request $request){
        $response= $httpClient->request('GET', "https://api-adresse.data.gouv.fr/search/", array(
            'query' => array(
                'q' => $request->query->get('q'),
               

            )
        ));
        return new Response( $response->getContent() );
    }
     /**
     * @Route("/api/postal", name="api-postal", methods={"GET"})
     */
    public function postal(HttpClientInterface $httpClient, Request $request){
        $response= $httpClient->request('GET', "https://api-adresse.data.gouv.fr/search/", array(
            'query' => array(
                'q' => $request->query->get('q'),
                'limit' => $request->query->get('limit'),
                
                

            )
        ));
        return new Response( $response->getContent() );
    }

     /**
     * @Route("/dashboard", name="dashboard", methods={"GET", "POST"})
     */
    public function dashboard()
   {
    return $this->render("user/dashboard.html.twig");
   }

}