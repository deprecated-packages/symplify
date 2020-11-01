<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Functions;

use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

if (! function_exists('Symplify\PackageBuilder\Functions\service_polyfill')) {
    function service_polyfill(string $serviceId): ReferenceConfigurator
    {
        // Symfony 5.1+
        if (function_exists('Symfony\Component\DependencyInjection\Loader\Configurator\service')) {
            return service($serviceId);
        }

        // Symfony 4.4-
        return ref($serviceId);
    }
}
