<?php declare(strict_types=1);

namespace Symplify\FlexLoader\Tests\Flex\FlexLoader\Source;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\FlexLoader\Flex\FlexLoader;
use Symplify\PackageBuilder\HttpKernel\SimpleKernelTrait;

final class SwapKernel extends Kernel
{
    use SimpleKernelTrait; // so temp dirs are in /tmp, not here
    use MicroKernelTrait;

    public function __construct(string $environment, bool $debug)
    {
        // rand is for container rebuild
        parent::__construct($environment . random_int(1, 1000), $debug);

        new FlexLoader($this->getProjectDir(), $environment);
    }

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
    }
}
