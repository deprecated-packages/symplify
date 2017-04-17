<?php declare(strict_types=1);

namespace Symplify\ModularRouting\Tests\Routing;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symplify\ModularRouting\Tests\Routing\AbstractRouteCollectionProviderSource\MissingFileRouteCollectionProvider;

final class AbstractRouteCollectionProviderTest extends TestCase
{
    /**
     * @expectedException \Symplify\ModularRouting\Exception\FileNotFoundException
     */
    public function testMissingFiles(): void
    {
        $missingFileRouteCollectionProvider = new MissingFileRouteCollectionProvider(
            new LoaderResolver()
        );
        $missingFileRouteCollectionProvider->getRouteCollection();
    }
}
