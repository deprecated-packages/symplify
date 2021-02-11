<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Yaml;

use Nette\Utils\Strings;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symplify\PackageBuilder\Strings\StringFormatConverter;

/**
 * @copy of https://github.com/symplify/symplify/blob/d4beda1b1af847599aa035ead755e03db81c7247/packages/easy-coding-standard/src/Yaml/CheckerServiceParametersShifter.php
 *
 * Before:
 *
 * services:
 *      # fixer
 *      ArrayFixer:
 *          syntax: short
 *      # sniff
 *      ArraySniff:
 *          syntax: short
 *
 * After:
 *
 * services:
 *      # fixer
 *      ArrayFixer:
 *          calls:
 *              - ['configure', [{'syntax' => 'short'}]
 *      # sniff
 *      ArraySniff:
 *          parameters:
 *              $syntax: 'short'
 */
final class CheckerServiceParametersShifter
{
    /**
     * @var string
     */
    private const SERVICES_KEY = 'services';

    /**
     * @var string
     */
    private const SERVICE_KEYWORDS_KEY_STATIC = 'serviceKeywords';

    /**
     * @var string
     */
    private const SERVICE_KEYWORDS_KEY_CONST = 'SERVICE_KEYWORDS';

    /**
     * @var string[]
     */
    private $serviceKeywords = [];

    /**
     * @var StringFormatConverter
     */
    private $stringFormatConverter;

    public function __construct()
    {
        $this->stringFormatConverter = new StringFormatConverter();
        $this->initializeServiceKeywords();
    }

    /**
     * @param mixed[] $configuration
     * @return mixed[]
     */
    public function process(array $configuration): array
    {
        if (! isset($configuration[self::SERVICES_KEY])) {
            return $configuration;
        }
        if (! is_array($configuration[self::SERVICES_KEY])) {
            return $configuration;
        }
        $configuration[self::SERVICES_KEY] = $this->processServices($configuration[self::SERVICES_KEY]);

        return $configuration;
    }

    /**
     * @param mixed[] $services
     * @return mixed[]
     */
    private function processServices(array $services): array
    {
        foreach ($services as $serviceName => $serviceDefinition) {
            if (! $this->isCheckerClass($serviceName)) {
                continue;
            }

            if ($serviceDefinition === null) {
                continue;
            }

            if ($serviceDefinition === []) {
                continue;
            }

            if (Strings::endsWith($serviceName, 'Fixer')) {
                $services = $this->processFixer($services, $serviceName, $serviceDefinition);
            }

            if (Strings::endsWith($serviceName, 'Sniff')) {
                $services = $this->processSniff($services, $serviceName, $serviceDefinition);
            }

            // cleanup parameters
            $services = $this->cleanupParameters($services, $serviceDefinition, $serviceName);
        }

        return $services;
    }

    private function isCheckerClass(string $checker): bool
    {
        return Strings::endsWith($checker, 'Fixer') || Strings::endsWith($checker, 'Sniff');
    }

    /**
     * @param mixed[] $services
     * @param mixed[] $serviceDefinition
     * @return mixed[]
     */
    private function processFixer(array $services, string $checker, array $serviceDefinition): array
    {
        foreach (array_keys($serviceDefinition) as $key) {
            if ($this->isReservedKey($key)) {
                continue;
            }

            $serviceDefinition = $this->stringFormatConverter->camelCaseToUnderscoreInArrayKeys($serviceDefinition);

            $services[$checker]['calls'] = [['configure', [$serviceDefinition]]];
        }

        return $services;
    }

    /**
     * @param mixed[] $services
     * @param mixed[] $serviceDefinition
     * @return mixed[]
     */
    private function processSniff(array $services, string $checker, array $serviceDefinition): array
    {
        // move parameters to property setters
        foreach ($serviceDefinition as $key => $value) {
            if ($this->isReservedKey($key)) {
                continue;
            }

            $key = $this->stringFormatConverter->underscoreAndHyphenToCamelCase($key);

            $services[$checker]['properties'][$key] = $this->escapeValue($value);
        }

        return $services;
    }

    /**
     * @param mixed[] $services
     * @param mixed[] $serviceDefinition
     * @return mixed[]
     */
    private function cleanupParameters(array $services, array $serviceDefinition, string $serviceName): array
    {
        foreach (array_keys($serviceDefinition) as $key) {
            if ($this->isReservedKey($key)) {
                continue;
            }

            unset($services[$serviceName][$key]);
        }

        return $services;
    }

    /**
     * @param string|int|bool $key
     */
    private function isReservedKey($key): bool
    {
        if (! is_string($key)) {
            return false;
        }

        return in_array($key, $this->serviceKeywords, true);
    }

    /**
     * @return mixed|mixed[]|string
     */
    private function escapeValue($value)
    {
        if (! is_array($value) && ! is_string($value)) {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as $key => $nestedValue) {
                $value[$key] = $this->escapeValue($nestedValue);
            }

            return $value;
        }

        return Strings::replace($value, '#^@#', '@@');
    }

    private function initializeServiceKeywords(): void
    {
        $reflectionClass = new ReflectionClass(YamlFileLoader::class);
        /** @var array<string, mixed> $constants */
        $constants = $reflectionClass->getConstants();
        if (array_key_exists(self::SERVICE_KEYWORDS_KEY_CONST, $constants)) {
            /** @var string[] $serviceKeywordsProperty */
            $serviceKeywordsProperty = $constants[self::SERVICE_KEYWORDS_KEY_CONST];
            $this->serviceKeywords = $serviceKeywordsProperty;
            return;
        }

        /** @var array<string, mixed> $staticProperties */
        $staticProperties = $reflectionClass->getStaticProperties();
        /** @var string[] $serviceKeywordsProperty */
        $serviceKeywordsProperty = $staticProperties[self::SERVICE_KEYWORDS_KEY_STATIC];
        $this->serviceKeywords = $serviceKeywordsProperty;
    }
}
