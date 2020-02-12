<?php

namespace App\Controller;

use Stripe\Charge;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * 
 * @Route("/payment", name="payment")
 */
class PaymentController extends AbstractController
{
    
    private $publicKey;
    private $privateKey;

    public function __construct(){

        $this->publicKey = $_ENV['STRIPE_PUBLIC_KEY'];
        $this->privateKey = $_ENV['STRIPE_PRIVATE_KEY'];
    
    }

    /**
     * @Route("", name="_create")
     */
    public function index(SessionInterface $session)
    {
        $reservation = $session->get('reservation');
        $amount = $reservation->getPrice();
        
        return $this->render('payment/index.html.twig', [
                'publicKey' => $this->publicKey,
                'privateKey' => $this->privateKey,
                'amount' =>  $amount,
        ]);
    }

    /**
     * @Route("/verification", name="_charge")
     *
     * @param Request $request
     */
    public function charge(Request $request, SessionInterface $session)
    {
        $reservation = $session->get('reservation');
        $amount = $reservation->getPrice();

         \Stripe\Stripe::setApiKey($this->privateKey);
        try
        {
            $charge = \Stripe\Charge::create([
                'amount' => $amount*100,
                'currency' => 'eur',
                'description' => 'commande '.$reservation->getSerial(),
                'source' => $request->request->get('stripeToken'),
            ]);
        } catch (\Exception $e)
        {
            $this->addFlash('red', "Le paiement a été refusé.");
            return $this->redirectToRoute('home');
            // TODO erase dump.
            dump($e);
        }
            // transaction ID
            $charge = $charge->id;
            $this->addFlash('green', "Le paiement est OK");
            return $this->redirectToRoute('home');
    }

}
