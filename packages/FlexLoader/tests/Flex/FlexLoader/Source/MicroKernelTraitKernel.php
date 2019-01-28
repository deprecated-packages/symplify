<?php declare(strict_types=1);

namespace Symplify\FlexLoader\Tests\Flex\FlexLoader\Source;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\FlexLoader\Flex\FlexLoader;
use Symplify\PackageBuilder\HttpKernel\SimpleKernelTrait;

final class MicroKernelTraitKernel extends Kernel
{
    use SimpleKernelTrait; // so temp dirs are in /tmp, not here
    use MicroKernelTrait;

    /**
     * @var FlexLoader
     */
    private $flexLoader;

    public function __construct(string $environment, bool $debug)
    {
        // rand is for container rebuild
        parent::__construct($environment . random_int(1, 1000), $debug);

        $this->flexLoader = new FlexLoader($environment, $this->getProjectDir());
    }

    /**
     * for testing purposes
     */
    public function getProjectDir(): string
    {
        return __DIR__ . '/project-dir-micro-kernel';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return $this->flexLoader->loadBundles();
    }

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        // @todo test
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $this->flexLoader->loadConfigs($containerBuilder, $loader, [__DIR__ . '/extra-dir/*']);
    }
}
