<?php

namespace App\Controller;

use App\Entity\Stays;
use App\Entity\Travel;
use App\Entity\Traveler;
use App\Form\TravelerType;
use App\Entity\Reservation;
use App\Form\TravelersType;
use App\Form\ReservationOptionType;
use App\Repository\StaysRepository;
use App\Repository\OptionsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationController extends AbstractController
{
    /**
     * @Route("/reservation", name="reservation_index")
     */
    public function index(Request $request, SessionInterface $session, StaysRepository $stayRepository, OptionsRepository $optionRepository)
    {
        
        $id = $request->query->get('stayid');

        $stay = $stayRepository->find($id);
        
        $reservation = new Reservation();

        $reservation->addStay($stay);
        $reservation->setUser($this->getUser());

        $session->set('reservation', $reservation);

        return $this->render('reservation/index.html.twig', [
            'stay' => $stay
        ]);
    }

    /**
     * @route("/reservation/configure/{id}", name="reservation_configure")
     */
    public function configure(Stays $stays, SessionInterface $session, Request $request, OptionsRepository $repo)
    {
        $reservation = $session->get('reservation');
        dd($reservation->getOptions());

        $form = $this->createForm(ReservationOptionType::class, $reservation);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 

            dd($reservation);

        }

        return $this->render('reservation/configureTravel.html.twig', [
            'stays' => $stays,
            'form' => $form->createView(),
        ]);
        
    }

    /**
     * @route("/reservation/add/{id}", name="reservation_add")
     */
    public function add($id, SessionInterface $session)
    {
        

        $reservation = new Reservation();
       
        dd($reservation);
       

            //quantité de base
            $reservation[$id] = 1;
       
        

        $session->set('reservation', $reservation);

        //dd($session->get('reservation'));
        return $this->redirectToRoute("reservation_index");
    }


    /**
     * @route("/reservation/configure/configureUser/{id}", name="reservation_configure_user")
     */
    public Function configureUser( Request $request)
    {
        
        //form collection
        $traveler = new Reservation();
        
        $form = $this->createForm( TravelersType::class, $traveler);

        $form->handleRequest( $request );
        
        if( $form->isSubmitted() && $form->isValid() ){

            $em = $this->getDoctrine()->getManager();
            
            $em->persist($traveler);
            
        }
        //fin form collection
        
        return $this->render('reservation/configureUser.html.twig', [
            'form' => $form->createView(), //for form collection
            
        ]);

    }

    /**
     * @route("/reservation/remove/{id}", name= "reservation_remove")
     */
    public Function remove($id, SessionInterface $session)
    {
        $reservation = $session->get('reservation', []);

        if(!empty($reservation[$id]))
        {
            unset($reservation[$id]);
        }

        $session->set('reservation', $reservation);

        return $this->redirectToRoute("reservation_index");
    }
}
