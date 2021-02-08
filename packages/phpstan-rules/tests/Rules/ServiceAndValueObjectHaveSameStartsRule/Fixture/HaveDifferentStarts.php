<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ServiceAndValueObjectHaveSameStartsRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PHPStanRules\Tests\Rules\ServiceAndValueObjectHaveSameStartsRule\Source\ChangeMethodVisibilityRector;
use Symplify\PHPStanRules\Tests\Rules\ServiceAndValueObjectHaveSameStartsRule\Source\ConfigureValueObject;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ChangeMethodVisibilityRector::class)
        ->call('configure', [[
            ChangeMethodVisibilityRector::METHOD_VISIBILITIES => ValueObjectInliner::inline([
                new ConfigureValueObject(),
                new ConfigureValueObject(),
            ]),
        ]]);
};
