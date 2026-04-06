<?php

namespace App\Mate;

use App\Repository\TravelRepository;
use Mcp\Capability\Attribute\McpTool;

class TravelRecommendationTool
{
    public function __construct(
        private TravelRepository $travelRepository,
    ) {
    }

    #[McpTool(
        name: 'recommend-travels',
        description: 'Recommande des voyages DésirVoyage basés sur un budget, une durée en jours '
                   .'et un mois de départ souhaité. Retourne les voyages triés par pertinence.'
    )]
    public function recommend(
        float $budget = 0,
        int $durationDays = 0,
        int $departureMonth = 0,
    ): array {
        $qb = $this->travelRepository->createQueryBuilder('t')
            ->leftJoin('t.stays', 's')
            ->leftJoin('t.categories', 'c')
            ->addSelect('s', 'c');

        // Filtre par budget
        if ($budget > 0) {
            $qb->andWhere('s.price <= :budget')
               ->setParameter('budget', $budget);
        }

        // Filtre par mois de départ
        if ($departureMonth >= 1 && $departureMonth <= 12) {
            $qb->andWhere('MONTH(s.starDate) = :month')
               ->setParameter('month', $departureMonth);
        }

        $travels = $qb->getQuery()->getResult();

        $results = [];
        foreach ($travels as $travel) {
            $stays = $travel->getStays()->toArray();

            // Filtre par durée si spécifiée
            if ($durationDays > 0) {
                $stays = array_filter($stays, function ($stay) use ($durationDays) {
                    $diff = $stay->getStarDate()?->diff($stay->getEndDate());

                    return null !== $diff && $diff->days <= $durationDays;
                });
            }

            if (empty($stays)) {
                continue;
            }

            $stayData = array_map(fn ($stay) => [
                'departure' => $stay->getDepature(),
                'arrival' => $stay->getArrival(),
                'startDate' => $stay->getStarDate()?->format('d/m/Y'),
                'endDate' => $stay->getEndDate()?->format('d/m/Y'),
                'price' => $stay->getPrice(),
                'stock' => $stay->getStock(),
            ], $stays);

            $results[] = [
                'name' => $travel->getName(),
                'subtitle' => $travel->getSubtitle(),
                'description' => $travel->getDescriptions(),
                'category' => $travel->getCategories()?->getTitle(),
                'minPrice' => $travel->getMinPrice(),
                'availableStays' => $stayData,
            ];
        }

        return [
            'success' => true,
            'count' => count($results),
            'recommendations' => $results,
        ];
    }
}
