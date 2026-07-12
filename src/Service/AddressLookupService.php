<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Adaptateur de l'API publique d'adresses, avec entrée bornée et délai réseau.
 */
final readonly class AddressLookupService
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * @return array<array-key, mixed>
     */
    public function search(string $query): array
    {
        $query = mb_substr(trim($query), 0, 200);
        if ('' === $query) {
            return [];
        }

        return $this->httpClient->request('GET', 'https://api-adresse.data.gouv.fr/search/', [
            'query' => ['q' => $query],
            'timeout' => 5.0,
        ])->toArray(false);
    }
}
