<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{


    private $encoder;
   
    public function __construct( UserPasswordEncoderInterface $encoder ){
        $this->encoder = $encoder;
    }
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request):Response
    {
        if($this->getUser() )
        {
            return $this->redirectToRoute('home');
        }    
        
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() )
        {
          $password = $this->encoder->encodePassword($user, $user->getPassword() );
          $user->setPassword($password); 
          

          $em = $this->getDoctrine()->getManager();
          $em->persist( $user );
          $em->flush();

        }
        return $this->render('user/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
