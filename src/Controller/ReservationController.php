<?php

namespace App\Controller;

use App\Entity\Stays;
use App\Entity\Travel;
use Doctrine\ORM\Query\Expr\Func;
use App\Repository\StaysRepository;
use App\Repository\TravelRepository;
use Twig\Node\Expression\FunctionExpression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationController extends AbstractController
{
    /**
     * @Route("/reservation", name="reservation_index")
     */
    public function index(SessionInterface $session, StaysRepository $stayRepository, TravelRepository $travelRepository)
    {
        $reservation = $session->get('reservation',[]);

        $reservationWithData = [];

        foreach($reservation as $id => $quantity)
        {
            $reservationWithData[] = [
                'stays' => $stayRepository->find($id),
                'quantity' => $quantity

            ];
        }
        //dd($reservationWithData);
        return $this->render('reservation/index.html.twig', [
            'items' => $reservationWithData
        ]);
    }

    /**
     * @route("/reservation/add/{id}", name="reservation_add")
     */
    public function add($id, SessionInterface $session)
    {
        
       
        $reservation = $session->get('reservation', []);

        if(!empty($reservation[$id]))
        {
            //quantité +1 si tu clique 
            $reservation[$id]++;
        }else{

            //quantité de base
            $reservation[$id] = 1;
        }
        

        $session->set('reservation', $reservation);

        //dd($session->get('reservation'));
        return $this->redirectToRoute("reservation_index");
    }

    /**
     * @route("/reservation/configure/{id}", name="reservation_configure")
     */
    public Function configure(Stays $stays)
    {
        

        return $this->render('reservation/configureTravel.html.twig', [
            'stays' => $stays,
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
