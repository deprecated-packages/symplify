<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use PhpCsFixer\Fixer\ClassNotation\FinalInternalClassFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use SlevomatCodingStandard\Sniffs\Namespaces\ReferenceUsedNamesOnlySniff;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/config.yaml');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->set(FinalInternalClassFixer::class);

    $services->set(ReferenceUsedNamesOnlySniff::class)
        ->property('searchAnnotations', true)
        ->property('allowFallbackGlobalFunctions', true)
        ->property('allowFallbackGlobalConstants', true)
        ->property('allowPartialUses', false);

    $services->load('Symplify\CodingStandard\Fixer\\', __DIR__ . '/../src/Fixer');

    $services->load('Symplify\CodingStandard\Sniffs\\', __DIR__ . '/../src/Sniffs');

    $services->set(PhpdocLineSpanFixer::class);
};
