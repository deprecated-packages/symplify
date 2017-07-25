<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Neon\Loader;

use Nette\DI\Config\Loader;
use Nette\Utils\Strings;
use ReflectionClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symplify\EasyCodingStandard\SniffRunner\Exception\ClassNotFoundException;
use Symplify\PackageBuilder\Exception\Neon\InvalidSectionException;

final class NeonLoader implements LoaderInterface
{
    /**
     * @var LoaderResolverInterface
     */
    private $resolver;

    /**
     * @var ContainerBuilder|ContainerInterface
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
     * @param null|string $type
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

        // add parameters
        if (isset($content['parameters'])) {
            $content += $content['parameters'];
            unset($content['parameters']);
        }

        foreach ($content as $key => $value) {
            $this->containerBuilder->setParameter($key, $value);
        }

        // register services
        if (isset($content['services'])) {
            // @todo: make fail proof, possible use YamlLoader with reflection?
            if (! is_array($content['services'])) {
                return;
            }

            $content = $this->prepareServicesToYamlFormat($content, $resource); // ~ => null
            $this->symfonyConfigServiceSectionParser($content, $resource);
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

    /**
     * @param mixed[] $content
     * @return mixed[]
     */
    private function prepareServicesToYamlFormat(array $content, string $resource): array
    {
        foreach ($content['services'] as $name => $service) {
            if ($service === '~') {
                $service = $name;

                if (! class_exists($service)) {
                    throw new ClassNotFoundException(sprintf(
                        'Class "%s" was not found while loading a config file "%s".',
                        $service,
                        $resource
                    ));
                }

                $content['services'][$name] = ['class' => $service];
            }
        }

        return $content;
    }

    /**
     * @param mixed[] $content
     */
    private function symfonyConfigServiceSectionParser(array $content, string $resource): void
    {
        $yamlFileLoader = new YamlFileLoader($this->containerBuilder, new FileLocator);
        $classReflection = new ReflectionClass(YamlFileLoader::class);
        $parseDefinitionsMethod = $classReflection->getMethod('parseDefinitions');
        $parseDefinitionsMethod->setAccessible(true);
        $parseDefinitionsMethod->invoke($yamlFileLoader, $content, $resource);
    }
}
