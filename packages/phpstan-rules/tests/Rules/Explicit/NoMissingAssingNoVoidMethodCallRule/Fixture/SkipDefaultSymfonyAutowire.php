<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class SkipDefaultSymfonyAutowire
{
    public function __construct(ContainerConfigurator $containerConfigurator)
    {
        $services = $containerConfigurator->services();

        $services->defaults()
            ->public()
            ->autowire();
    }
}
