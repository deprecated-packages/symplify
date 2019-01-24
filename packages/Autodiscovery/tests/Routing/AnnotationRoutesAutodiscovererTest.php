<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Routing;

use Symfony\Component\Routing\Router;
use Symplify\Autodiscovery\Routing\AnnotationRoutesAutodiscoverer;
use Symplify\Autodiscovery\Tests\AbstractAppKernelAwareTestCase;

/**
 * @see AnnotationRoutesAutodiscoverer
 */
final class AnnotationRoutesAutodiscovererTest extends AbstractAppKernelAwareTestCase
{
    public function test(): void
    {
        /** @var Router $router */
        $router = $this->container->get('router');
        $annotationNames = array_keys($router->getRouteCollection()->all());

        $this->assertContains('it-works', $annotationNames);
        $this->assertContains('also-works', $annotationNames);
    }
}
