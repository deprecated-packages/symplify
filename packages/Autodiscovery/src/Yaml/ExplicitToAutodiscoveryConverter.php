<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Yaml;

use Nette\Utils\Strings;
use ReflectionClass;
use Symfony\Component\Filesystem\Filesystem;
use Symplify\Autodiscovery\Arrays;
use Symplify\Autodiscovery\Php\InterfaceAnalyzer;
use function Safe\realpath;
use function Safe\sprintf;

final class ExplicitToAutodiscoveryConverter
{
    /**
     * @var bool
     */
    private $enableAutowire = false;

    /**
     * @var bool
     */
    private $removeService = false;

    /**
     * @var bool
     */
    private $enableAutoconfigure = false;

    /**
     * @var bool
     */
    private $removeSinglyImplementedAlaises = false;

    /**
     * @var string[]
     */
    private $classes = [];

    /**
     * @var string[]
     */
    private $possibleExcludedDirectories = [
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
     * @var InterfaceAnalyzer
     */
    private $interfaceAnalyzer;

    /**
     * @var TagAnalyzer
     */
    private $tagAnalyzer;

    public function __construct(
        Filesystem $filesystem,
        CommonNamespaceResolver $commonNamespaceResolver,
        InterfaceAnalyzer $interfaceAnalyzer,
        TagAnalyzer $tagAnalyzer
    ) {
        $this->filesystem = $filesystem;
        $this->commonNamespaceResolver = $commonNamespaceResolver;
        $this->interfaceAnalyzer = $interfaceAnalyzer;
        $this->tagAnalyzer = $tagAnalyzer;
    }

    /**
     * @param mixed[] $yaml
     * @return mixed[]
     */
    public function convert(
        array $yaml,
        string $filePath,
        int $nestingLevel,
        bool $removeSinglyImplementedAliases
    ): array {
        $this->reset($removeSinglyImplementedAliases);

        // nothing to change
        if (! isset($yaml[YamlKey::SERVICES])) {
            return $yaml;
        }

        foreach ($yaml[YamlKey::SERVICES] as $name => $service) {
            $yaml = $this->processService($yaml, $service, $name);
        }

        $yaml = $this->completeAutodiscovery($yaml, $filePath, $nestingLevel);

        if ($this->enableAutoconfigure) {
            $yaml = $this->completeDefaultsKeyTrue($yaml, YamlKey::AUTOCONFIGURE);
        }

        if ($this->enableAutowire) {
            $yaml = $this->completeDefaultsKeyTrue($yaml, YamlKey::AUTOWIRE);
        }

        return $yaml;
    }

    private function reset(bool $removeSinglyImplementedAliases): void
    {
        $this->classes = [];
        $this->enableAutowire = false;
        $this->enableAutoconfigure = false;
        $this->removeSinglyImplementedAlaises = $removeSinglyImplementedAliases;
    }

    /**
     * @param mixed[] $yaml
     * @param string|mixed[]|null $service
     * @return mixed[]
     */
    private function processService(array $yaml, $service, string $name): array
    {
        $this->removeService = false;

        if ($this->shouldSkipService($service, $name)) {
            return $yaml;
        }

        if (is_array($service)) {
            [$yaml, $service, $name] = $this->processArrayService($yaml, $service, $name);
        }

        // anonymous service
        if ($service === null) {
            $this->classes[] = $name;
            $this->removeService = true;
        }

        // update
        if ($this->removeService) {
            unset($yaml[YamlKey::SERVICES][$name]);
        } else {
            $yaml[YamlKey::SERVICES][$name] = $service;
        }

        return $yaml;
    }

    /**
     * @param mixed[] $yaml
     * @return mixed[]
     */
    private function completeAutodiscovery(array $yaml, string $filePath, int $nestingLevel): array
    {
        $commonNamespaces = $this->commonNamespaceResolver->resolve($this->classes, $nestingLevel);
        $groupedServices = $this->groupServicesByNamespaces($this->classes, $commonNamespaces);

        foreach ($groupedServices as $namespace => $classes) {
            $namespaceKey = $namespace . '\\';
            if (isset($yaml[YamlKey::SERVICES][$namespaceKey])) {
                continue;
            }

            $relativeServicesLocation = $this->resolveCommonRelativePath($classes, $filePath);
            $yaml[YamlKey::SERVICES][$namespaceKey] = [
                YamlKey::RESOURCE => $relativeServicesLocation,
            ];

            $excludedDirectories = $this->resolveExcludedDirectories($filePath, $relativeServicesLocation);
            if (count($excludedDirectories)) {
                $exclude = $relativeServicesLocation . sprintf('/{%s}', implode(',', $excludedDirectories));
                $yaml[YamlKey::SERVICES][$namespaceKey]['exclude'] = $exclude;
            }

            $this->enableAutowire = true;
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
        $yaml[YamlKey::SERVICES] = array_merge([YamlKey::DEFAULTS => [$key => true]], $yaml[YamlKey::SERVICES]);

        return $yaml;
    }

    /**
     * @param mixed|mixed[] $service
     */
    private function shouldSkipService($service, string $name): bool
    {
        $class = $service['class'] ?? $name;

        // skip no-namespace class naming
        if (! Strings::contains($class, '\\')) {
            return true;
        }

        // is in vendor?
        if (class_exists($class)) {
            $reflectionClass = new ReflectionClass($class);
            if (Strings::match($reflectionClass->getFileName(), '#/vendor/#')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed[] $yaml
     * @param mixed[] $service
     * @return mixed[]
     */
    private function processArrayService(array $yaml, array $service, string $name): array
    {
        $service = $this->processAutowire($service);
        $service = $this->processTags($service);

        $this->processAlias($service, $name);

        // is only named services
        if (Arrays::hasOnlyKey($service, 'class')) {
            unset($yaml[YamlKey::SERVICES][$name]);

            $name = $service['class'];
            $service = null;
            $yaml[YamlKey::SERVICES][$name] = $service;
        }

        // is named service
        if (isset($service['class']) && is_string($name) && ! ctype_upper($name[0]) && ! class_exists($name)) {
            // @todo check is no where used in the script, regular would do

            unset($yaml[YamlKey::SERVICES][$name]);
            $name = $service['class'];
            unset($service['class']);

            $yaml[YamlKey::SERVICES][$name] = $service;
        }

        // normalize empty service
        if ($service === []) {
            $service = null;
        }

        return [$yaml, $service, $name];
    }

    /**
     * @param string[] $services
     * @param string[] $commonNamespaces
     * @return string[][]
     */
    private function groupServicesByNamespaces(array $services, array $commonNamespaces): array
    {
        $groupedServicesByNamespace = [];
        foreach ($commonNamespaces as $commonNamespace) {
            foreach ($services as $service) {
                if (Strings::startsWith($service, $commonNamespace . '\\')) {
                    $groupedServicesByNamespace[$commonNamespace][] = $service;
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
        if ($absoluteServicesLocation === false) {
            return [];
        }

        $excludedDirectories = [];
        foreach ($this->possibleExcludedDirectories as $possibleExcludedDirectory) {
            $possibleDirectoryPath = $absoluteServicesLocation . '/' . $possibleExcludedDirectory;
            if (is_dir($possibleDirectoryPath)) {
                $excludedDirectories[] = $possibleExcludedDirectory;
            }
        }

        return $excludedDirectories;
    }

    /**
     * @param mixed[] $service
     * @return mixed[]
     */
    private function processAutowire(array $service): array
    {
        // remove autowire
        if (isset($service[YamlKey::AUTOWIRE])) {
            unset($service[YamlKey::AUTOWIRE]);
            $this->enableAutowire = true;
        }

        return $service;
    }

    /**
     * @param mixed[] $service
     * @return mixed[]
     */
    private function processTags(array $service): array
    {
        if (isset($service[YamlKey::TAGS])) {
            if ($this->tagAnalyzer->isAutoconfiguredTags($service[YamlKey::TAGS])) {
                unset($service[YamlKey::TAGS]);
                $this->enableAutoconfigure = true;
            }
        }

        return $service;
    }

    /**
     * @param mixed[] $service
     */
    private function processAlias(array $service, string $name): void
    {
        if (Arrays::hasOnlyKey($service, 'alias') && $this->removeSinglyImplementedAlaises) {
            if ($this->interfaceAnalyzer->isInterfaceOnlyImplementation($name, $service['alias'])) {
                $this->removeService = true;
            }
        }
    }

    private function getRelativeClassLocation(string $class, string $configFilePath): string
    {
        if (! class_exists($class)) {
            // assumption of traditional location
            $classDirectory = realpath(__DIR__ . '/../../src');
        } else {
            $reflectionClass = new ReflectionClass($class);

            $classDirectory = dirname($reflectionClass->getFileName());
        }

        $configDirectory = realpath(dirname($configFilePath));

        $relativePath = $this->filesystem->makePathRelative($classDirectory, $configDirectory);

        return rtrim($relativePath, '/');
    }
}
