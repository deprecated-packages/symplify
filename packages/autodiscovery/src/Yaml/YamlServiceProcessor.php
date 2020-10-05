<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Yaml;

use Nette\Utils\Strings;
use ReflectionClass;
use Symplify\Autodiscovery\SymplifyArrays;
use Symplify\Autodiscovery\ValueObject\ServiceConfig;

final class YamlServiceProcessor
{
    /**
     * @var string
     * @see https://regex101.com/r/nR1pcK/1
     */
    private const VENDOR_DIRECTORY_REGEX = '#/vendor/#';

    /**
     * @var bool
     */
    private $removeService = false;

    /**
     * @var TagAnalyzer
     */
    private $tagAnalyzer;

    /**
     * @var ServiceConfig
     */
    private $serviceConfig;

    /**
     * @var SymplifyArrays
     */
    private $symplifyArrays;

    public function __construct(TagAnalyzer $tagAnalyzer, SymplifyArrays $symplifyArrays)
    {
        $this->tagAnalyzer = $tagAnalyzer;
        $this->symplifyArrays = $symplifyArrays;
    }

    /**
     * @param mixed[] $yaml
     * @param string|mixed[]|null $service
     * @return mixed[]|mixed
     */
    public function process(
        array $yaml,
        $service,
        string $name,
        string $filter,
        ServiceConfig $serviceConfig
    ): array {
        $this->serviceConfig = $serviceConfig;
        $this->removeService = false;

        if ($this->shouldSkipService($service, $name, $filter)) {
            return $yaml;
        }

        if (is_array($service)) {
            [$yaml, $service, $name] = $this->processArrayService($yaml, $service, $name);
        }

        // anonymous service
        if ($service === null) {
            $this->serviceConfig->addClass($name);
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
     * @param mixed|mixed[] $service
     */
    private function shouldSkipService($service, string $name, string $filter): bool
    {
        $class = $service['class'] ?? $name;

        // skip no-namespace class naming
        if (! Strings::contains($class, '\\')) {
            return true;
        }

        if ($filter && ! Strings::contains($class, $filter)) {
            return true;
        }

        // is in vendor?
        if (class_exists($class)) {
            $reflectionClass = new ReflectionClass($class);
            if (Strings::match($reflectionClass->getFileName(), self::VENDOR_DIRECTORY_REGEX)) {
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

        // is only named services
        if ($this->symplifyArrays->hasOnlyKey($service, 'class')) {
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
     * @param mixed[] $service
     * @return mixed[]
     */
    private function processAutowire(array $service): array
    {
        // remove autowire
        if (isset($service[YamlKey::AUTOWIRE])) {
            unset($service[YamlKey::AUTOWIRE]);
            $this->serviceConfig->enableAutowire();
        }

        return $service;
    }

    /**
     * @param mixed[] $service
     * @return mixed[]
     */
    private function processTags(array $service): array
    {
        if (! isset($service[YamlKey::TAGS])) {
            return $service;
        }

        if ($this->tagAnalyzer->isAutoconfiguredTags($service[YamlKey::TAGS])) {
            unset($service[YamlKey::TAGS]);
            $this->serviceConfig->enableAutoconfigure();
        }

        return $service;
    }
}
