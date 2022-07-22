<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class SkipSymfonyContainerConfigurator
{
    public function run(ContainerConfigurator $containerConfigurator)
    {
        $services =  $containerConfigurator->services();
        $services->set('value');

        $services->set('value')
            ->call('this', ['that']);
    }
}
