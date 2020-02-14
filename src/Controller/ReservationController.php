<?php

namespace App\Controller;

use DateTime;
use App\Entity\Stays;
use App\Entity\Reservation;
use App\Form\TravelersType;
use App\Service\MakeSerialService;
use App\Form\ReservationOptionType;
use App\Repository\StaysRepository;
use App\Repository\OptionsRepository;
use App\Service\StockManagementService;
use App\Service\ReservationMergeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ReservationController extends AbstractController
{
    /**
     * Start configure travel
     * 
     * @Route("/reservation", name="reservation_index")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function index(Request $request, SessionInterface $session, StaysRepository $stayRepository, OptionsRepository $optionRepository)
    {
        // retrieve travel by get method 
        $id = $request->query->get('stayid');
        // find stay by id
        $stay = $stayRepository->find($id);
        // create a new objetc travel
        $reservation = new Reservation();
        // add stay in reservation
        $reservation->addStay($stay);
        $reservation->setUser($this->getUser());
        // create a new session and add reservation
        $session->set('reservation', $reservation);

        return $this->render('reservation/index.html.twig', [
            'stay' => $stay
        ]);
    }

    /**
     * Configure option
     * 
     * @route("/reservation/configure/{id}", name="reservation_configure")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function configure(Stays $stays, SessionInterface $session, Request $request, 
    $id , ReservationMergeService $seservationMergeService)
    {
        // get session
        $reservation = $session->get('reservation');
        
        $seservationMergeService->reservationOptionsMerge($reservation );
        // insert $reservation in form
        $form = $this->createForm(ReservationOptionType::class, $reservation);
        // retrieve request
        $form->handleRequest($request);
            
        if ($form->isSubmitted() && $form->isValid()) { 

            $session->set('reservation', $reservation);
            return $this->redirectToRoute('traveler_configure', ['id' => $id]);
        }

        return $this->render('reservation/configureTravel.html.twig', [
            'stays' => $stays,
            'form' => $form->createView(),
            'reservation' => $reservation
        ]);
    }

    /**
     * congigure travelers
     * 
     * @route("/reservation/configure/configureTravelers/{id}", name="traveler_configure")
     * 
     * @IsGranted("ROLE_USER")
     */
    public Function configureTravelers( Request $request, SessionInterface $session, $id)
    {
        if ($session->get('reservation') == null){
            return $this->redirectToRoute('reservation_list');
        };
        // get session
        $reservation = $session->get('reservation');
        // insert $reservation in form
        $form = $this->createForm( TravelersType::class,  $reservation);
        // retrieve request
        $form->handleRequest( $request );
        
        if( $form->isSubmitted() && $form->isValid() ){

            $session->set('reservation', $reservation);
            return $this->redirectToRoute('summary_reservation');
        }
        
        return $this->render('reservation/configureTravelers.html.twig', [
            'form' => $form->createView(),
            'reservation' => $reservation
        ]);

    }

    /**
     * show configuration travel and calcul coast
     * 
     * @route("/reservation/summary", name="summary_reservation")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function summary(SessionInterface $session)
    { 
        if ($session->get('reservation') == null){
            return $this->redirectToRoute('reservation_list');
        };
        $reservation = $session->get('reservation');
        // get numbers of travelers
        $nbtravelers =  count($reservation->getTravelers());
        // if there is no registered traveler
        if ($nbtravelers < 1){

            $id = $reservation->getStays()[0]->getId();

            $this->addFlash(
                'orange', 
                'OUPS ! Il n\'y a personne d\'enregistré sur ce voyoyage'
            );
            
            return $this->redirectToRoute("traveler_configure", ['id' => $id] );

        }
       
        // get price stays for 1 traveler
        $stayPrice = $reservation->getStays()[0]->getPrice();
        // get price for all options
        $options = $reservation->getOptions();
        $optionsPrice = 0;
        foreach($options as $option){
            $optionsPrice += $option->getPrice();
        }
        // set total price
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
     * Valid for pay travel
     * 
     * @route("/reservation/validate/", name="reservation_validate")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function validate(SessionInterface $session, MakeSerialService $service, 
     ReservationMergeService $seservationMergeService, StockManagementService $stockManagementService)
    {
        if ($session->get('reservation') == null){
            return $this->redirectToRoute('reservation_list');
        };
        // reservation in session
        $reservation = $session->get('reservation');

        // make serial and date
        if($reservation->getSerial() == null ){
            
            $reservation->setSerial($service->makeSerial());
            $reservation->setCreateddate(new \DateTime('now') );
            // stock management
            $realStock = $stockManagementService->decrementStock($reservation);
            
            // if stays is not in stock 
            if(count ($reservation->getTravelers()) > $realStock ){
            
            $this->addFlash(
                'red darken-4', 
                'Il ne reste pas suffisamment de place 
                merci de choisir un autre voyage ou une autre période '
            );

           return $this->redirectToRoute("travel_list");
        }

        } else {
            $reservation->setUpdateAt(new \DateTime('now') );
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        // mannage persist in service
        $merged = $seservationMergeService->reservationMerge($reservation);

        $entityManager->persist($merged);

        $entityManager->flush();

        $id = $merged->getId();
        $session->clear();
        $session->set('id', $id);
        return $this->redirectToRoute('payment_create', ['id' => $id]);
    }

    /**
     * reservations list
     * 
     * @route("/reservation/list/", name= "reservation_list")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function reservationsList()
    {
        $user = $this->getUser();
        $reservation = $user->getReservations();

        return $this->render('reservation/reservationlist.html.twig', [
                'user' => $user,
                'reservation' => $reservation,
        ]);

    }

    /**
     * remove travel in session
     * 
     * @route("/reservation/remove/", name= "reservation_remove")
     * 
     * @IsGranted("ROLE_USER")
     */
    public function remove(SessionInterface $session)
    {
        $session->set('reservation', null);

        $this->addFlash('red darken-4', 'Vous avez annulé votre voyage');
        
        return $this->redirectToRoute("home");
    }
}