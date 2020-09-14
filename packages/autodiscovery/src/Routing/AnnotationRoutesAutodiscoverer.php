<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Routing;

use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\Autodiscovery\Contract\AutodiscovererInterface;
use Symplify\Autodiscovery\Finder\AutodiscoveryFinder;

/**
 * @see \Symplify\Autodiscovery\Tests\Routing\AnnotationRoutesAutodiscovererTest
 */
final class AnnotationRoutesAutodiscoverer implements AutodiscovererInterface
{
    /**
     * @var RouteCollectionBuilder
     */
    private $routeCollectionBuilder;

    /**
     * @var AutodiscoveryFinder
     */
    private $autodiscoveryFinder;

    public function __construct(
        RouteCollectionBuilder $routeCollectionBuilder,
        AutodiscoveryFinder $autodiscoveryFinder
    ) {
        $this->routeCollectionBuilder = $routeCollectionBuilder;
        $this->autodiscoveryFinder = $autodiscoveryFinder;
    }

    public function autodiscover(): void
    {
        foreach ($this->autodiscoveryFinder->getControllerDirectories() as $controllerDirectoryFileInfo) {
            $this->routeCollectionBuilder->import($controllerDirectoryFileInfo->getRealPath(), '/', 'annotation');
        }
    }
}
