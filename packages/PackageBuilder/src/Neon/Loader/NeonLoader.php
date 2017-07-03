<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Neon\Loader;

use Nette\DI\Config\Loader;
use Nette\Utils\Strings;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\PackageBuilder\Exception\Neon\InvalidSectionException;

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
     * @param string[] $allowedSections
     */
    public function load($resource, $allowedSections = ['parameters', 'services', 'includes']): void
    {
        $neonLoader = new Loader;
        $content = $neonLoader->load($resource);

        if (count($allowedSections)) {
            $this->validateContentSections($content, $allowedSections);
        }

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

    /**
     * @param mixed[] $content
     * @param string[] $allowedSections
     */
    private function validateContentSections(array $content, array $allowedSections): void
    {
        foreach ($content as $key => $values) {
            if (in_array($key, $allowedSections)) {
                unset($content[$key]);
            }
        }

        if (! count($content)) {
            return;
        }

        throw new InvalidSectionException(sprintf(
            'Invalid sections found: "%s". Only "%s" are allowed.',
            implode('", "', array_keys($content)),
            implode('", "', $allowedSections)
        ));
    }
}
