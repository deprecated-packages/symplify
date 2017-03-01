<?php declare(strict_types=1);

namespace Symplify\ModularLatteFilters\Tests\Filter;

use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class MathFilters implements LatteFiltersProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            'double' => function (int $value) {
                return $value * 2;
            }
        ];
    }
}
