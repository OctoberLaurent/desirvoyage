<?php

namespace App\Service\Payment;

use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

/**
 * Adaptateur Stripe de {@see PaymentGatewayInterface}.
 * La clé secrète est injectée (jamais exposée côté client), la clé publique reste
 * gérée par le contrôleur pour le rendu du formulaire.
 */
final readonly class StripePaymentGateway implements PaymentGatewayInterface
{
    public function __construct(string $secretKey)
    {
        Stripe::setApiKey($secretKey);
    }

    public function charge(int $amountCents, string $currency, string $description, string $source): ChargedPayment
    {
        try {
            $charge = Charge::create([
                'amount' => $amountCents,
                'currency' => $currency,
                'description' => $description,
                'source' => $source,
            ]);
        } catch (ApiErrorException $e) {
            throw PaymentFailedException::fromReason($e->getMessage());
        }

        /** @var string $id */
        $id = $charge->id;
        /** @var int $amount */
        $amount = $charge->amount;

        return new ChargedPayment($id, $amount);
    }
}
