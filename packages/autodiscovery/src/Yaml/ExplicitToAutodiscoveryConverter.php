<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Yaml;

use Nette\Utils\Strings;
use ReflectionClass;
use Symfony\Component\Filesystem\Filesystem;
use Symplify\Autodiscovery\Exception\ClassLocationNotFoundException;
use Symplify\Autodiscovery\Exception\ClassNotFoundException;
use Symplify\Autodiscovery\ValueObject\ServiceConfig;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ExplicitToAutodiscoveryConverter
{
    /**
     * @var string[]
     */
    private const POSSIBLE_EXCLUDED_DIRECTORIES = [
        'Entity',
        'Entities',
        'Exception',
        'Exceptions',
        'Contract',
        'Contracts',
    ];

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var CommonNamespaceResolver
     */
    private $commonNamespaceResolver;

    /**
     * @var YamlServiceProcessor
     */
    private $yamlServiceProcessor;

    /**
     * @var ServiceConfig
     */
    private $serviceConfig;

    public function __construct(
        Filesystem $filesystem,
        CommonNamespaceResolver $commonNamespaceResolver,
        YamlServiceProcessor $yamlServiceProcessor
    ) {
        $this->filesystem = $filesystem;
        $this->commonNamespaceResolver = $commonNamespaceResolver;
        $this->yamlServiceProcessor = $yamlServiceProcessor;
    }

    /**
     * @param mixed[] $yaml
     * @return mixed[]
     */
    public function convert(array $yaml, string $filePath, int $nestingLevel, string $filter): array
    {
        $this->serviceConfig = new ServiceConfig();

        // nothing to change
        if (! isset($yaml[YamlKey::SERVICES])) {
            return $yaml;
        }

        foreach ($yaml[YamlKey::SERVICES] as $name => $service) {
            $yaml = $this->yamlServiceProcessor->process($yaml, $service, $name, $filter, $this->serviceConfig);
        }

        $yaml = $this->completeAutodiscovery($yaml, $filePath, $nestingLevel);

        if ($this->serviceConfig->isAutoconfigure()) {
            $yaml = $this->completeDefaultsKeyTrue($yaml, YamlKey::AUTOCONFIGURE);
        }

        if ($this->serviceConfig->isAutowire()) {
            $yaml = $this->completeDefaultsKeyTrue($yaml, YamlKey::AUTOWIRE);
        }

        return $yaml;
    }

    /**
     * @param mixed[] $yaml
     * @return mixed[]
     */
    private function completeAutodiscovery(array $yaml, string $filePath, int $nestingLevel): array
    {
        $commonNamespaces = $this->commonNamespaceResolver->resolve($this->serviceConfig, $nestingLevel);
        $groupedServices = $this->groupServicesByNamespaces($this->serviceConfig, $commonNamespaces);

        foreach ($groupedServices as $namespace => $classes) {
            $namespaceKey = $namespace . '\\';
            if (isset($yaml[YamlKey::SERVICES][$namespaceKey])) {
                continue;
            }

            $relativeServicesLocation = $this->resolveCommonRelativePath($classes, $filePath);
            $yaml[YamlKey::SERVICES][$namespaceKey] = [YamlKey::RESOURCE => $relativeServicesLocation];

            $excludedDirectories = $this->resolveExcludedDirectories($filePath, $relativeServicesLocation);
            if (count($excludedDirectories) > 0) {
                $exclude = $relativeServicesLocation . sprintf('/{%s}', implode(',', $excludedDirectories));
                $yaml[YamlKey::SERVICES][$namespaceKey]['exclude'] = $exclude;
            }

            $this->serviceConfig->enableAutowire();
        }

        return $yaml;
    }

    /**
     * @param mixed[] $yaml
     * @return mixed[]
     */
    private function completeDefaultsKeyTrue(array $yaml, string $key): array
    {
        if (isset($yaml[YamlKey::SERVICES][YamlKey::DEFAULTS][$key])) {
            $yaml[YamlKey::SERVICES][YamlKey::DEFAULTS][$key] = true;
            return $yaml;
        }

        // yes "_defaults", but no "autowire" section
        if (isset($yaml[YamlKey::SERVICES][YamlKey::DEFAULTS])) {
            $yaml[YamlKey::SERVICES][YamlKey::DEFAULTS] = array_merge(
                [$key => true],
                $yaml[YamlKey::SERVICES][YamlKey::DEFAULTS]
            );

            return $yaml;
        }

        // no "_defaults" section
        $yaml[YamlKey::SERVICES] = array_merge([
            YamlKey::DEFAULTS => [
                $key => true,
            ],
        ], $yaml[YamlKey::SERVICES]);

        return $yaml;
    }

    /**
     * @param string[] $commonNamespaces
     * @return string[][]
     */
    private function groupServicesByNamespaces(ServiceConfig $serviceConfig, array $commonNamespaces): array
    {
        $groupedServicesByNamespace = [];
        foreach ($commonNamespaces as $commonNamespace) {
            foreach ($serviceConfig->getClasses() as $class) {
                if (Strings::startsWith($class, $commonNamespace . '\\')) {
                    $groupedServicesByNamespace[$commonNamespace][] = $class;
                    continue;
                }
            }
        }

        return $groupedServicesByNamespace;
    }

    /**
     * @param string[] $classes
     */
    private function resolveCommonRelativePath(array $classes, string $filePath): string
    {
        $relativeClassLocations = [];
        foreach ($classes as $class) {
            $relativeClassLocations[] = $this->getRelativeClassLocation($class, $filePath);
        }

        return rtrim(Strings::findPrefix($relativeClassLocations), '/');
    }

    /**
     * @return string[]
     */
    private function resolveExcludedDirectories(string $configFilePath, string $absoluteServicesLocation): array
    {
        $absoluteServicesLocation = realpath(dirname($configFilePath) . '/' . $absoluteServicesLocation);
        if (! $absoluteServicesLocation) {
            return [];
        }

        $excludedDirectories = [];
        foreach (self::POSSIBLE_EXCLUDED_DIRECTORIES as $possibleExcludedDirectory) {
            $possibleDirectoryPath = $absoluteServicesLocation . '/' . $possibleExcludedDirectory;
            if (is_dir($possibleDirectoryPath)) {
                $excludedDirectories[] = $possibleExcludedDirectory;
            }
        }

        sort($excludedDirectories);

        return $excludedDirectories;
    }

    private function getRelativeClassLocation(string $class, string $configFilePath): string
    {
        if (! class_exists($class)) {
            // assumption of traditional location
            $classDirectory = realpath(__DIR__ . '/../../src');
        } else {
            $reflectionClass = new ReflectionClass($class);

            $fileName = $reflectionClass->getFileName();
            if (! $fileName) {
                throw new ClassNotFoundException(sprintf(
                    'Class "%s" from config "%s" was not found in any file. Make sure it exists.',
                    $class,
                    $configFilePath
                ));
            }

            $classDirectory = dirname($fileName);
        }

        if (! $classDirectory) {
            throw new ClassLocationNotFoundException(sprintf('Location for "%s" class was not found.', $class));
        }

        $fileInfo = new SmartFileInfo($configFilePath);
        $configDirectory = dirname($fileInfo->getRealPath());

        $relativePath = $this->filesystem->makePathRelative($classDirectory, $configDirectory);

        return rtrim($relativePath, '/');
    }
}
