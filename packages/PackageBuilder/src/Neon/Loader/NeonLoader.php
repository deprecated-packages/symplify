<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Neon\Loader;

use Nette\DI\Config\Loader;
use Nette\Utils\Strings;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class NeonLoader implements LoaderInterface
{
    /**
     * @var LoaderResolverInterface
     */
    private $resolver;

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @param ContainerBuilder|ContainerInterface $containerBuilder
     */
    public function __construct(ContainerInterface $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
    }

    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function supports($resource, $type = null): bool
    {
        return Strings::endsWith($resource, '.neon');
    }

    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function load($resource, $type = null): void
    {
        $neonLoader = new Loader;
        $content = $neonLoader->load($resource);

        if (isset($content['parameters'])) {
            $content += $content['parameters'];
            unset($content['parameters']);
        }

        foreach ($content as $key => $value) {
            $this->containerBuilder->setParameter($key, $value);
        }
    }

    public function getResolver(): LoaderResolverInterface
    {
        return $this->resolver;
    }

    public function setResolver(LoaderResolverInterface $resolver): void
    {
        $this->resolver = $resolver;
    }
}
