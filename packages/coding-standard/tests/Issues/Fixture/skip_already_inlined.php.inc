<?php

namespace Symplify\CodingStandard\Tests\Issues\Fixture;

use Rector\MagicDisclosure\Rector\Isset_\UnsetAndIssetToMethodCallRector;
use Rector\MagicDisclosure\ValueObject\IssetUnsetToMethodCall;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symplify\SymfonyPhpConfig\inline_value_objects;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(UnsetAndIssetToMethodCallRector::class)
        ->call('configure', [[
            UnsetAndIssetToMethodCallRector::ISSET_UNSET_TO_METHOD_CALL => inline_value_objects([
                new IssetUnsetToMethodCall('SomeContainer', 'hasService', 'removeService'),
            ]),
        ]]);
};
