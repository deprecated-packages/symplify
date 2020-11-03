<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFunctionCallInMethodCallRule\Fixture;

use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

final class SkipNamespacedFunction
{
    public function something()
    {
        $this->process(ref('reference'));
    }

    private function process(\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator $ref)
    {
    }
}
