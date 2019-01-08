<?php declare(strict_types=1);

namespace Symplify\Statie\GithubContributorsThanker\Twig;

use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use function Safe\usort;

final class SortingFilterProvider implements FilterProviderInterface
{
    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            'sortItemsByKey' => function (array $items, string $key) {
                usort($items, function (array $firstItem, array $secondItem) use ($key): int {
                    return strtolower($firstItem[$key]) <=> strtolower($secondItem[$key]);
                });

                return $items;
            },
        ];
    }
}
