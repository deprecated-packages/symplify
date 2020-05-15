<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Routing;

use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\Autodiscovery\Contract\AutodiscovererInterface;
use Symplify\Autodiscovery\Finder\AutodiscoveryFinder;

final class AnnotationRoutesAutodiscoverer implements AutodiscovererInterface
{
    /**
     * @var RouteCollectionBuilder
     */
    private $routeCollectionBuilder;

    /**
     * @var AutodiscoveryFinder
     */
    private $fileSystem;

    public function __construct(RouteCollectionBuilder $routeCollectionBuilder, AutodiscoveryFinder $fileSystem)
    {
        $this->routeCollectionBuilder = $routeCollectionBuilder;
        $this->fileSystem = $fileSystem;
    }

    public function autodiscover(): void
    {
        foreach ($this->fileSystem->getControllerDirectories() as $controllerDirectoryFileInfo) {
            $this->routeCollectionBuilder->import($controllerDirectoryFileInfo->getRealPath(), '/', 'annotation');
        }
    }
}
