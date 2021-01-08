<?php

declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\ConverterWorker;

use Nette\Neon\Entity;
use Nette\Utils\Strings;
use Symfony\Component\Yaml\Yaml;

final class ServiceConverterWorker
{
    /**
     * @var string
     */
    private const ARGUMENTS = 'arguments';

    /**
     * @var string
     */
    private const SETUP = 'setup';

    /**
     * @param mixed[] $servicesData
     * @return mixed[]
     */
    public function convert(array $servicesData): array
    {
        $servicesData = $this->convertServiceNeonEntities($servicesData);
        $servicesData = $this->convertStringNameServices($servicesData);

        foreach ($servicesData as $name => $service) {
            if (is_string($service)) {
                if (is_string($name) && $service === '~') {
                    $servicesData[$name] = null;
                    continue;
                }

                $service = $this->convertStringService($service);
            } elseif (is_array($service)) {
                // named service
                $service = $this->convertNamedService($service);
            }

            $service = $this->convertSetupToCalls($service);
            $service = $this->convertArguments($service);

            $servicesData[$name] = $service;
        }

        return $servicesData;
    }

    /**
     * @param mixed[] $servicesData
     * @return mixed[]
     */
    private function convertServiceNeonEntities(array $servicesData): array
    {
        foreach ($servicesData as $name => $service) {
            if (! $service instanceof Entity) {
                continue;
            }

            $servicesData = $this->convertServiceEntity($servicesData, $name, $service);
        }

        return $servicesData;
    }

    /**
     * @return mixed[]
     */
    private function convertStringNameServices(array $servicesData): array
    {
        foreach ($servicesData as $name => $service) {
            if (! is_int($name)) {
                continue;
            }

            $servicesData = $this->convertStringNameService($servicesData, $name, $service);
        }

        return $servicesData;
    }

    /**
     * @return mixed[]
     */
    private function convertStringService(string $service): array
    {
        // probably factory, @see https://symfony.com/doc/current/service_container/factories.html
        if (Strings::contains($service, '::')) {
            return [
                'factory' => explode('::', $service),
            ];
            // probably alias, @see https://symfony.com/doc/current/service_container/alias_private.html#aliasing
        }

        if (Strings::startsWith($service, '@')) {
            return [
                'alias' => $service,
            ];
            // probably service
        }

        return [
            'class' => $service,
        ];
    }

    private function convertNamedService(array $service): array
    {
        if (! isset($service['class'])) {
            return $service;
        }
        if (! $service['class'] instanceof Entity) {
            return $service;
        }

        if ($service['class']->attributes !== []) {
            $service[self::ARGUMENTS] = $service['class']->attributes;
        }
        $service['class'] = $service['class']->value;

        return $service;
    }

    private function convertSetupToCalls($service)
    {
        if (! is_array($service)) {
            return $service;
        }

        if (! isset($service[self::SETUP])) {
            return $service;
        }

        foreach ((array) $service[self::SETUP] as $key => $value) {
            if ($value instanceof Entity) {
                $service[self::SETUP][$key] = [$value->value, $value->attributes];
            }
        }

        // inline calls - requires fixup in YamlOutputFormatter
        $setupYamlContent = Yaml::dump($service[self::SETUP], 1, 4, Yaml::DUMP_OBJECT);
        $service['calls'] = $setupYamlContent;
        unset($service[self::SETUP]);

        return $service;
    }

    private function convertArguments($service)
    {
        if (! is_array($service)) {
            return $service;
        }

        if (! isset($service[self::ARGUMENTS])) {
            return $service;
        }

        foreach ((array) $service[self::ARGUMENTS] as $key => $value) {
            if (! $value instanceof Entity) {
                continue;
            }

            // environment value! @see https://symfony.com/blog/new-in-symfony-3-4-advanced-environment-variables
            if ($value->value === '@env::get') {
                $environmentVariable = $value->attributes[0];
                $service[self::ARGUMENTS][$key] = sprintf('%%env(%s)%%', $environmentVariable);
            }
        }

        return $service;
    }

    /**
     * @param int|string $name
     * @return mixed[]
     */
    private function convertServiceEntity(array $servicesData, $name, Entity $entity): array
    {
        $class = $entity->value;

        $serviceData = [
            'class' => $class,
            self::ARGUMENTS => $entity->attributes,
        ];

        // class-named service
        if (is_int($name)) {
            // is namespaced class?
            if (Strings::contains($serviceData['class'], '\\')) {
                unset($serviceData['class']);
            }

            // remove old name
            unset($servicesData[$name]);

            $name = $class;
        }

        $servicesData[$name] = $serviceData;

        return $servicesData;
    }

    /**
     * @param mixed|null $service
     */
    private function convertStringNameService(array $servicesData, int $name, $service): array
    {
        if (! is_string($service)) {
            return $servicesData;
        }

        // just single-class
        unset($servicesData[$name]);

        $name = $service;

        $servicesData[$name] = null;

        return $servicesData;
    }
}
