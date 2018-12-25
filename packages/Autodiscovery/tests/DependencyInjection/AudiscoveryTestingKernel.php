<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\Autodiscovery\Doctrine\DoctrineEntityMappingAutodiscoverer;
use Symplify\Autodiscovery\Routing\AnnotationRoutesAutodiscoverer;
use Symplify\Autodiscovery\Translation\TranslationPathAutodiscoverer;
use Symplify\Autodiscovery\Twig\TwigPathAutodiscoverer;
use Symplify\PackageBuilder\HttpKernel\SimpleKernelTrait;

final class AudiscoveryTestingKernel extends Kernel
{
    use SimpleKernelTrait;
    use MicroKernelTrait;

    public function getProjectDir(): string
    {
        return __DIR__ . '/../KernelProjectDir';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new FrameworkBundle(), new TwigBundle(), new DoctrineBundle()];
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/config_test.yaml');

        (new TranslationPathAutodiscoverer($containerBuilder))->autodiscover();
        (new DoctrineEntityMappingAutodiscoverer($containerBuilder))->autodiscover();
        (new TwigPathAutodiscoverer($containerBuilder))->autodiscover();
    }

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        (new AnnotationRoutesAutodiscoverer($routeCollectionBuilder, $this->getContainerBuilder()))->autodiscover();
    }
}
