<?php

namespace App\Service\Payment;

use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

/**
 * Stripe adapter for {@see PaymentGatewayInterface}.
 * The secret key is injected and never exposed to the client. The public key
 * remains managed by the controller for form rendering.
 */
final readonly class StripePaymentGateway implements PaymentGatewayInterface
{
    public function __construct(string $secretKey)
    {
        Stripe::setApiKey($secretKey);
    }

    #[\Override]
    public function charge(int $amountCents, string $currency, string $description, string $source, string $idempotencyKey): ChargedPayment
    {
        try {
            $charge = Charge::create(
                [
                    'amount' => $amountCents,
                    'currency' => $currency,
                    'description' => $description,
                    'source' => $source,
                ],
                ['idempotency_key' => $idempotencyKey],
            );
        } catch (ApiErrorException $e) {
            throw PaymentFailedException::fromReason($e->getMessage());
        }

        $id = $charge->id;
        $amount = $charge->amount;

        return new ChargedPayment($id, $amount);
    }
}
