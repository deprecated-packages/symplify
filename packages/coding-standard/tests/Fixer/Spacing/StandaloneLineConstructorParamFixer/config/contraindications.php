<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Basic\BracesFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\CodingStandard\Fixer\Spacing\StandaloneLineConstructorParamFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(StandaloneLineConstructorParamFixer::class);
    $services->set(BracesFixer::class);
    $services->set(LineLengthFixer::class);
};
