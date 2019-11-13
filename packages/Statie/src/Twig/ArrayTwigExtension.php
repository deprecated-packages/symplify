<?php declare(strict_types=1);

namespace Symplify\Statie\Twig;

use Iterator;
use Symplify\Statie\Templating\ArrayUtils;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ArrayTwigExtension extends AbstractExtension
{
    /**
     * @var ArrayUtils
     */
    private $arrayUtils;

    public function __construct(ArrayUtils $arrayUtils)
    {
        $this->arrayUtils = $arrayUtils;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): Iterator
    {
        // usage in Twig: {% set entries = sort_by_field(entries, 'name', 'desc') %}
        yield new TwigFunction('sort_by_field', function (
            array $items,
            string $sortBy,
            string $direction = 'ASC'
        ): array {
            return $this->arrayUtils->sortByField($items, $sortBy, $direction);
        });

        // usage in Twig: {% set entries = group_by_field(entries, 'country') %}
        yield new TwigFunction('group_by_field', function (array $items, string $groupBy): array {
            return $this->arrayUtils->groupByField($items, $groupBy);
        });
    }
}
