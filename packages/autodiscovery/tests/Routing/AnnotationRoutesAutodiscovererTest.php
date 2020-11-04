<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Routing;

use Symfony\Component\Routing\Router;
use Symplify\Autodiscovery\Routing\AnnotationRoutesAutodiscoverer;
use Symplify\Autodiscovery\Tests\Source\HttpKernel\AudiscoveryTestingKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

/**
 * @see AnnotationRoutesAutodiscoverer
 */
final class AnnotationRoutesAutodiscovererTest extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernel(AudiscoveryTestingKernel::class);
    }

    public function test(): void
    {
        /** @var Router $router */
        $router = static::$container->get('router');
        $annotationNames = array_keys($router->getRouteCollection()->all());

        $this->assertContains('it-works', $annotationNames);
        $this->assertContains('also-works', $annotationNames);
    }
}
