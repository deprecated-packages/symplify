<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Source\HttpKernel;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\Autodiscovery\Discovery;

final class AudiscoveryTestingKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * @var Discovery
     */
    private $discovery;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);
        $this->discovery = new Discovery($this->getProjectDir());
    }

    public function getProjectDir(): string
    {
        return __DIR__ . '/../KernelProjectDir';
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/autodisocovery_test_kernel';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/autodisocovery_log_kernel';
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

        $this->discovery->discoverTranslations($containerBuilder);
        $this->discovery->discoverEntityMappings($containerBuilder);
        $this->discovery->discoverTemplates($containerBuilder);
    }

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        $this->discovery->discoverRoutes($routeCollectionBuilder);
    }
}
