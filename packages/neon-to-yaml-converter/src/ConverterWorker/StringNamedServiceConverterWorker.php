<?php

declare(strict_types=1);

namespace Symplify\NeonToYamlConverter\ConverterWorker;

final class StringNamedServiceConverterWorker
{
    /**
     * @return mixed[]
     */
    public function convert(array $servicesData): array
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
    public function convertSingle(string $service): array
    {
        // probably factory, @see https://symfony.com/doc/current/service_container/factories.html
        if (\str_contains($service, '::')) {
            return [
                'factory' => explode('::', $service),
            ];
            // probably alias, @see https://symfony.com/doc/current/service_container/alias_private.html#aliasing
        }

        if (\str_starts_with($service, '@')) {
            return [
                'alias' => $service,
            ];
            // probably service
        }

        return [
            'class' => $service,
        ];
    }

    /**
     * @param mixed|null $service
     * @return mixed[]
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
