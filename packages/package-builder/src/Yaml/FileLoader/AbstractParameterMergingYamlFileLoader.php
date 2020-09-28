<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Yaml\FileLoader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symplify\PackageBuilder\Exception\Yaml\InvalidParametersValueException;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
use Symplify\PackageBuilder\Yaml\ParametersMerger;

/**
 * The need:
 * - https://github.com/symfony/symfony/issues/26713
 * - https://github.com/symfony/symfony/pull/21313#issuecomment-372037445
 */
abstract class AbstractParameterMergingYamlFileLoader extends YamlFileLoader
{
    /**
     * @var string
     */
    private const PARAMETERS_KEY = 'parameters';

    /**
     * @var ParametersMerger
     */
    private $parametersMerger;

    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;

    public function __construct(ContainerBuilder $containerBuilder, FileLocatorInterface $fileLocator)
    {
        $this->parametersMerger = new ParametersMerger();
        $this->privatesCaller = new PrivatesCaller();

        parent::__construct($containerBuilder, $fileLocator);
    }

    /**
     * Same as parent, just merging parameters instead overriding them
     *
     * @see https://github.com/symplify/symplify/pull/697
     *
     * @param string|null $type
     */
    public function load($resource, $type = null): void
    {
        $path = $this->locator->locate($resource);

        /** @var mixed[]|null $content */
        $content = $this->loadFile($path);
        $this->container->fileExists($path);

        // empty file
        if ($content === null) {
            return;
        }

        // imports: $this->parseImports($content, $path);
        $this->privatesCaller->callPrivateMethod($this, 'parseImports', $content, $path);

        // parameters
        if (isset($content[self::PARAMETERS_KEY])) {
            $this->ensureParametersIsArray($content, $path);

            foreach ($content[self::PARAMETERS_KEY] as $key => $value) {
                // $this->resolveServices($value, $path, true),
                $resolvedValue = $this->privatesCaller->callPrivateMethod(
                    $this,
                    'resolveServices',
                    $value,
                    $path,
                    true
                );

                // only this section is different
                if ($this->container->hasParameter($key)) {
                    $newValue = $this->parametersMerger->merge(
                        $resolvedValue,
                        $this->container->getParameter($key)
                    );

                    $this->container->setParameter($key, $newValue);
                } else {
                    $this->container->setParameter($key, $resolvedValue);
                }
            }
        }

        // extensions: $this->loadFromExtensions($content);
        $this->privatesCaller->callPrivateMethod($this, 'loadFromExtensions', $content);

        /**
         * services - not accessible, private parent properties, luckily not needed
         * - $this->anonymousServicesCount = 0;
         * - $this->anonymousServicesSuffix = ContainerBuilder::hash($path);
         */
        $directory = dirname($path);
        $this->setCurrentDir($directory);
        try {
            // $this->parseDefinitions($content, $path);
            $this->privatesCaller->callPrivateMethod($this, 'parseDefinitions', $content, $path);
        } finally {
            $this->instanceof = [];
        }
    }

    /**
     * @param mixed[] $content
     */
    private function ensureParametersIsArray(array $content, string $path): void
    {
        if (is_array($content[self::PARAMETERS_KEY])) {
            return;
        }

        throw new InvalidParametersValueException(sprintf(
            'The "parameters" key should contain an array in "%s". Check your YAML syntax.',
            $path
        ));
    }
}
