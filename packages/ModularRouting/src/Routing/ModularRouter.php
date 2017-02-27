<?php declare(strict_types=1);

namespace Symplify\ModularRouting\Routing;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symplify\ModularRouting\Contract\Routing\ModularRouterInterface;
use Symplify\ModularRouting\Contract\Routing\RouteCollectionProviderInterface;

final class ModularRouter implements ModularRouterInterface
{
    /**
     * @var RouteCollection
     */
    private $routeCollection;

    /**
     * @var RequestContext
     */
    private $requestContext;

    /**
     * @var UrlMatcherInterface
     */
    private $urlMatcher;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct()
    {
        $this->routeCollection = new RouteCollection;
    }

    public function addRouteCollectionProvider(RouteCollectionProviderInterface $routeCollectionProvider): void
    {
        $this->routeCollection->addCollection($routeCollectionProvider->getRouteCollection());
    }

    public function getRouteCollection(): RouteCollection
    {
        return $this->routeCollection;
    }

    public function setContext(RequestContext $requestContext): void
    {
        $this->requestContext = $requestContext;
    }

    /**
     * @param string $name
     * @param mixed[] $parameters
     * @param int $referenceType
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH): string
    {
        return $this->getUrlGenerator()
            ->generate($name, $parameters, $referenceType);
    }

    /**
     * @param string $pathinfo
     * @return array[]
     */
    public function match($pathinfo): array
    {
        return $this->getUrlMatcher()
            ->match($pathinfo);
    }

    public function getContext(): string
    {
        // this method is never used
        return '...';
    }

    private function getUrlGenerator(): UrlGeneratorInterface
    {
        if ($this->urlGenerator) {
            return $this->urlGenerator;
        }

        return $this->urlGenerator = new UrlGenerator($this->getRouteCollection(), $this->requestContext);
    }

    private function getUrlMatcher(): UrlMatcherInterface
    {
        if ($this->urlMatcher) {
            return $this->urlMatcher;
        }

        return $this->urlMatcher = new UrlMatcher($this->getRouteCollection(), $this->requestContext);
    }
}
