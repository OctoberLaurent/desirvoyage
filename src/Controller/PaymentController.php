<?php

namespace App\Controller;

use Stripe\Charge;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private $publicKey;
    private $privateKey;

    public function __construct(){

        $this->publicKey = $_ENV['STRIPE_PUBLIC_KEY'];
        $this->privateKey = $_ENV['STRIPE_PRIVATE_KEY'];
    
    }

    /**
     * @Route("/payment", name="payment")
     */
    public function index()
    {
        return $this->render('payment/index.html.twig', [
                'publicKey' => $this->publicKey,
                'privateKey' => $this->privateKey, 
        ]);
    }

    /**
     * @Route("/verification", name="_charge")
     *
     * @param Request $request
     */
    public function charge()
    {

        \Stripe\Stripe::setApiKey($this->privateKey);

        try
        {
            \Stripe\Charge::create([
                'amount' => 12,
                'currency' => 'eur',
                'description' => 'test',
                'source' => 'test',
            ]);
        } catch (\Exception $e)
        {
            $this->addFlash('warning', "Le paiement a été refusé.");
            return $this->redirectToRoute('home');
        }
        
    
    }

}
