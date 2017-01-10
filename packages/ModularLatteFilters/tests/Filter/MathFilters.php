<?php declare(strict_types=1);

namespace Zenify\ModularLatteFilters\Tests\Filter;

use Zenify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class MathFilters implements LatteFiltersProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFilters() : array
    {
        return [
            'double' => function (int $value) {
                return $value * 2;
            }
        ];
    }
}
