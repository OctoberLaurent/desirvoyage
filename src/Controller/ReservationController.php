<?php

namespace App\Controller;

use DateTime;
use App\Entity\Stays;
use App\Entity\Travel;
use App\Entity\Traveler;
use App\Form\TravelerType;
use App\Entity\Reservation;
use App\Form\TravelersType;
use App\Service\MakeSerialService;
use App\Form\ReservationOptionType;
use App\Repository\StaysRepository;
use App\Repository\OptionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
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

            return $this->redirectToRoute('summary_reservation');
        }
        
        return $this->render('reservation/configureTravelers.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @route("/reservation/summary", name="summary_reservation")
     */
    public function summary(SessionInterface $session)
    {
        $reservation = $session->get('reservation');

        // get price stays for 1 traveler
        $stayPrice = $reservation->getStays()[0]->getPrice();
        // get price for all options
        $options = $reservation->getOptions();
        $optionsPrice = 0;
        foreach($options as $option){
            $optionsPrice += $option->getPrice();
        }
        // get numbers of travelers
        $nbtravelers =  count($reservation->getTravelers())+1;

        $totalPriceOptions = ($optionsPrice)*$nbtravelers;
        $totalPrice = ($stayPrice+$optionsPrice)*$nbtravelers;

        $reservation->setPrice($totalPrice);

        return $this->render('reservation/summary.html.twig', [
            'reservation' => $reservation,
            'totalPrice' => $totalPrice,
            'totalPriceOptions' => $totalPriceOptions
        ]);
    }

    /**
     * @route("/reservation/validate/", name="reservation_validate")
     */
    public function validate(SessionInterface $session, MakeSerialService $service, StaysRepository $stayRepo)
    {
        $reservation = $session->get('reservation');
        $stock = $stayRepo->findStockByid($reservation->getStays()[0]->getId());
 
        if(count ($reservation->getTravelers()) > $stock ){
            
            $this->addFlash(
                'red darken-4', 
                'Il ne reste pas suffisamment de place 
                <br> merci de choisir un autre voyage ou une autre période '
            );

            return $this->redirectToRoute("travel_list");
        }

        $reservation->setSerial($service->makeSerial());
        $reservation->setCreateddate(new \DateTime('now') );
        
        $entityManager = $this->getDoctrine()->getManager();

        //
        $merged = $entityManager->merge($reservation);
        $merged->setTravelers( $reservation->getTravelers() );
        $merged->setOptions( $reservation->getOptions() );
        $stays = $reservation->getStays();
        $mstays = new ArrayCollection();
        foreach( $stays as $stay ){
            $mstays[] = $entityManager->merge( $stay );
        }
        $merged->setStays( $mstays );
        //
        $entityManager->persist($merged);

        $entityManager->flush();



        return $this->redirectToRoute("home");
    }

    /**
     * @route("/reservation/remove/", name= "reservation_remove")
     */
    public function remove(SessionInterface $session)
    {
        $session->set('reservation', null);

        $this->addFlash('red darken-4', 'Vous avez annulé votre voyage');
        
        return $this->redirectToRoute("home");
    }
}
