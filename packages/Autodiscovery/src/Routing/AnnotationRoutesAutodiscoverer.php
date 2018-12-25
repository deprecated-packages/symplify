<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Routing;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\Autodiscovery\Contract\AutodiscovererInterface;
use Symplify\Autodiscovery\FileSystem;

final class AnnotationRoutesAutodiscoverer implements AutodiscovererInterface
{
    /**
     * @var RouteCollectionBuilder
     */
    private $routeCollectionBuilder;

    /**
     * @var FileSystem
     */
    private $fileSystem;

    public function __construct(RouteCollectionBuilder $routeCollectionBuilder, ContainerBuilder $containerBuilder)
    {
        $this->routeCollectionBuilder = $routeCollectionBuilder;
        $this->fileSystem = new Filesystem($containerBuilder);
    }

    public function autodiscover(): void
    {
        foreach ($this->fileSystem->getControllerDirectories() as $controllerDirectoryFileInfo) {
            $this->routeCollectionBuilder->import($controllerDirectoryFileInfo->getRealPath(), '/', 'annotation');
        }
    }
}
