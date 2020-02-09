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
    public function configure(Stays $stays, SessionInterface $session, Request $request, $id)
    {
        $reservation = $session->get('reservation');

        $form = $this->createForm(ReservationOptionType::class, $reservation);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 

            $session->set('reservation', $reservation);
            return $this->redirectToRoute('traveler_configure', ['id' => $id]);
        }

        return $this->render('reservation/configureTravel.html.twig', [
            'stays' => $stays,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @route("/reservation/configure/configureTravelers/{id}", name="traveler_configure")
     */
    public Function configureTravelers( Request $request, SessionInterface $session, $id)
    {

        $reservation = $session->get('reservation');
        
        $form = $this->createForm( TravelersType::class,  $reservation);

        $form->handleRequest( $request );
        
        if( $form->isSubmitted() && $form->isValid() ){

            $session->set('reservation', $reservation);
            dd($reservation);
            
        }
        
        return $this->render('reservation/configureTravelers.html.twig', [
            'form' => $form->createView()
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
