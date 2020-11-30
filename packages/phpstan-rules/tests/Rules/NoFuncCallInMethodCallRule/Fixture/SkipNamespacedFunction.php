<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFuncCallInMethodCallRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

final class SkipNamespacedFunction
{
    public function something()
    {
        $this->process($this->getReferenceConfigurator());
    }

    private function process(\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator $ref)
    {
    }

    private function getReferenceConfigurator(): ReferenceConfigurator
    {
        if (function_exists('Symfony\Component\DependencyInjection\Loader\Configurator\service')) {
            return service('reference');
        }

        return ref('reference');
    }
}
