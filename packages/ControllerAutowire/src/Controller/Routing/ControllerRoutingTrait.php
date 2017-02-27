<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Controller\Routing;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

trait ControllerRoutingTrait
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }

    /**
     * @param string $route
     * @param mixed[] $parameters
     * @param int $referenceType
     */
    protected function generateUrl(
        string $route,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        return $this->router->generate($route, $parameters, $referenceType);
    }

    protected function redirect(string $url, int $status = 302): RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * @param string $route
     * @param mixed[] $parameters
     * @param int $status
     */
    protected function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
    {
        return $this->redirect($this->generateUrl($route, $parameters), $status);
    }
}
