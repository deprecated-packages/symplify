<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Routing;

use Symplify\Autodiscovery\Tests\AbstractContainerAwareTestCase;

final class AnnotationRoutesAutodiscoverTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        $router = $this->container->get('router');
        $annotationNames = array_keys($router->getRouteCollection()->all());

        $this->assertContains('it-works', $annotationNames);
        $this->assertContains('also-works', $annotationNames);
    }
}
