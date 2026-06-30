<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SortByFieldExtension extends AbstractExtension
{
    #[\Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter('sortByField', $this->sortByField(...)),
        ];
    }

    /**
     * @param array<int, mixed>|object $content
     *
     * @return array<int, mixed>
     */
    public function sortByField(array|object $content, string $sort_by, string $direction = 'asc'): array
    {
        if (is_object($content) && is_a($content, 'Doctrine\ORM\PersistentCollection')) {
            /** @var array<int, mixed> $content */
            $content = $content->toArray();
        }
        if (!is_array($content)) {
            throw new \InvalidArgumentException('Variable passed to the sortByField filter is not an array');
        } elseif (count($content) < 1) {
            /* @var array<int, mixed> $content */
            return $content;
        }
        @usort($content, function ($a, $b) use ($sort_by, $direction) {
            $flip = ('desc' === $direction) ? -1 : 1;
            if (is_array($a)) {
                $a_sort_value = $a[$sort_by];
            } elseif (is_object($a) && method_exists($a, 'get'.ucfirst($sort_by))) {
                /** @phpstan-ignore-next-line */
                $a_sort_value = $a->{'get'.ucfirst($sort_by)}();
            } else {
                /** @phpstan-ignore-next-line */
                $a_sort_value = is_object($a) ? $a->$sort_by : null;
            }
            if (is_array($b)) {
                $b_sort_value = $b[$sort_by];
            } elseif (is_object($b) && method_exists($b, 'get'.ucfirst($sort_by))) {
                /** @phpstan-ignore-next-line */
                $b_sort_value = $b->{'get'.ucfirst($sort_by)}();
            } else {
                /** @phpstan-ignore-next-line */
                $b_sort_value = is_object($b) ? $b->$sort_by : null;
            }
            if ($a_sort_value === $b_sort_value) {
                return 0;
            } elseif ($a_sort_value > $b_sort_value) {
                return 1 * $flip;
            }

            return -1 * $flip;
        });

        /* @var array<int, mixed> $content */
        return $content;
    }

    public function getName(): string
    {
        return 'sortbyfield_extension';
    }
}
