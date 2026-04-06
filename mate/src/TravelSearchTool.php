<?php

namespace App\Mate;

use App\Repository\CategoriesRepository;
use App\Repository\TravelRepository;
use Mcp\Capability\Attribute\McpTool;

class TravelSearchTool
{
    public function __construct(
        private TravelRepository $travelRepository,
        private CategoriesRepository $categoriesRepository,
    ) {
    }

    #[McpTool(
        name: 'search-travels',
        description: 'Recherche des voyages dans le catalogue DésirVoyage. '
                   .'Permet de filtrer par catégorie, destination (ville de départ) et prix maximum. '
                   .'Retourne une liste de voyages avec leurs détails.'
    )]
    public function search(
        string $category = '',
        string $departure = '',
        float $maxPrice = 0,
    ): array {
        $qb = $this->travelRepository->createQueryBuilder('t')
            ->leftJoin('t.categories', 'c')
            ->leftJoin('t.stays', 's')
            ->addSelect('c', 's');

        if ('' !== $category) {
            $qb->andWhere('c.title LIKE :category')
               ->setParameter('category', '%'.$category.'%');
        }

        if ('' !== $departure) {
            $qb->andWhere('s.depature LIKE :departure')
               ->setParameter('departure', '%'.$departure.'%');
        }

        if ($maxPrice > 0) {
            $qb->andWhere('s.price <= :maxPrice')
               ->setParameter('maxPrice', $maxPrice);
        }

        $travels = $qb->getQuery()->getResult();

        return array_map(fn ($travel) => [
            'name' => $travel->getName(),
            'subtitle' => $travel->getSubtitle(),
            'description' => $travel->getDescriptions(),
            'category' => $travel->getCategories()?->getTitle(),
            'minPrice' => $travel->getMinPrice(),
        ], $travels);
    }

    #[McpTool(
        name: 'list-categories',
        description: 'Liste toutes les catégories de voyages disponibles dans DésirVoyage.'
    )]
    public function listCategories(): array
    {
        $categories = $this->categoriesRepository->findAll();

        return array_map(fn ($category) => [
            'title' => $category->getTitle(),
            'slug' => $category->getSlug(),
        ], $categories);
    }
}
