<?php

declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\ConverterWorker;

use Nette\Neon\Entity;
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
     * @var EntityConverterWorker
     */
    private $entityConverterWorker;

    /**
     * @var StringNamedServiceConverterWorker
     */
    private $stringNamedServiceConverterWorker;

    public function __construct(
        EntityConverterWorker $entityConverterWorker,
        \Symplify\NeonToYamlConverter\ConverterWorker\StringNamedServiceConverterWorker $stringNamedServiceConverterWorker
    ) {
        $this->entityConverterWorker = $entityConverterWorker;
        $this->stringNamedServiceConverterWorker = $stringNamedServiceConverterWorker;
    }

    /**
     * @param mixed[] $servicesData
     * @return mixed[]
     */
    public function convert(array $servicesData): array
    {
        $servicesData = $this->entityConverterWorker->convertServiceNeonEntities($servicesData);
        $servicesData = $this->stringNamedServiceConverterWorker->convert($servicesData);

        foreach ($servicesData as $name => $service) {
            if (is_string($service)) {
                if (is_string($name) && $service === '~') {
                    $servicesData[$name] = null;
                    continue;
                }

                $service = $this->stringNamedServiceConverterWorker->convertSingle($service);
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
     * @return mixed[]
     */
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

    /**
     * @return mixed|mixed[]
     */
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

    /**
     * @return mixed|mixed[]
     */
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
}
