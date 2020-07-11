<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Tests\Controller\RouteFilter;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Route;
use Symplify\SymfonyStaticDumper\Controller\RouteFilter\RouteNameFilter;
use function array_keys;

final class RouteNameFilterTest extends TestCase
{
    public function testFilterOutRoutesWithoutMatchingNames(): void
    {
        $filter = new RouteNameFilter($routeNames = ['expected_name', 'expected_name_2']);

        $filteredRouteNames = array_keys($filter->filter([
            'not_expected_name' => new Route('/not/expected/name'),
            'expected_name' => new Route('/expected/name'),
            'expected_name_2' => new Route('/expected/name/2'),
        ]));

        $this->assertSame($routeNames, $filteredRouteNames);
    }
}
