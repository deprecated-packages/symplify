<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\CodingStandard\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/TokenRunner/ValueObject/*',
            __DIR__ . '/../src/Sniffs/*',
            __DIR__ . '/../src/Fixer/*',
            __DIR__ . '/../src/Rules/*',
        ]);

    $services->set(FunctionsAnalyzer::class);

    $services->set(ChangedFilesDetector::class);
};
