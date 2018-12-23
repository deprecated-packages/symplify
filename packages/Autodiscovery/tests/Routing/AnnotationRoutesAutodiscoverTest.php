<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Routing;

use Symplify\Autodiscovery\Tests\AbstractAppKernelAwareTestCase;

final class AnnotationRoutesAutodiscoverTest extends AbstractAppKernelAwareTestCase
{
    public function test(): void
    {
        $router = $this->container->get('router');
        $annotationNames = array_keys($router->getRouteCollection()->all());

        $this->assertContains('it-works', $annotationNames);
        $this->assertContains('also-works', $annotationNames);
    }
}
