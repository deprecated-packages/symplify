<?php declare(strict_types=1);

namespace Symplify\ModularRouting\Routing;

use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;
use Symplify\ModularRouting\Contract\Routing\RouteCollectionProviderInterface;
use Symplify\ModularRouting\Exception\FileNotFoundException;

abstract class AbstractRouteCollectionProvider implements RouteCollectionProviderInterface
{
    /**
     * @var LoaderResolverInterface
     */
    private $loaderResolver;

    public function setLoaderResolver(LoaderResolverInterface $loaderResolver)
    {
        $this->loaderResolver = $loaderResolver;
    }

    protected function loadRouteCollectionFromFile(string $path) : RouteCollection
    {
        if (! file_exists($path)) {
            throw new FileNotFoundException(
                sprintf('File "%s" was not found.', $path)
            );
        }

        $loader = $this->loaderResolver->resolve($path);
        if (null === $loader) {
            return new RouteCollection();
        }

        return $loader->load($path);
    }

    /**
     * @param string[] $paths
     */
    protected function loadRouteCollectionFromFiles(array $paths) : RouteCollection
    {
        $routeCollection = new RouteCollection();

        foreach ($paths as $path) {
            $routeCollection->addCollection($this->loadRouteCollectionFromFile($path));
        }

        return $routeCollection;
    }
}
