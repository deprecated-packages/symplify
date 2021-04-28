<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\DocBlockLineLengthFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(DocBlockLineLengthFixer::class)
        ->call('configure', [[
            DocBlockLineLengthFixer::LINE_LENGTH => 40,
        ]]);
};
