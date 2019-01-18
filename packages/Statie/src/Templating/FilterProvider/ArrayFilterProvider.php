<?php declare(strict_types=1);

namespace Symplify\Statie\Templating\FilterProvider;

use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use Symplify\Statie\Templating\ArrayUtils;

final class ArrayFilterProvider implements FilterProviderInterface
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
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            // usage in Twig: {% set entries = sort_by_field(entries, 'name', 'desc') %}
            // usage in Latte: {var $entries = ($entries|sort_by_field:'name', 'desc')}
            'sort_by_field' => function (array $items, string $sortBy, string $direction = 'ASC'): array {
                return $this->arrayUtils->sortByField($items, $sortBy, $direction);
            },

            // usage in Twig: {% set entries = group_by_field(entries, 'country') %}
            // usage in Latte: {var $entries = ($entries|group_by_field:'country')}
            'group_by_field' => function (array $items, string $groupBy): array {
                return $this->arrayUtils->groupByField($items, $groupBy);
            },
        ];
    }
}
