<?php

declare(strict_types=1);

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\ConsoleColorDiff\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use Symplify\ConsoleColorDiff\Differ\MarkdownDiffer;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\ConsoleColorDiff\\', __DIR__ . '/../src');

    $services->set(Differ::class);

    $services->set(SymfonyStyleFactory::class);

    $services->set(SymfonyStyle::class)
        ->factory([ref(SymfonyStyleFactory::class), 'create']);

    // markdown
    $services->set('markdownDiffOutputBuilder', UnifiedDiffOutputBuilder::class)
        ->factory([ref(CompleteUnifiedDiffOutputBuilderFactory::class), 'create']);

    $services->set('markdownDiffer', Differ::class)
        ->arg('$outputBuilder', ref('markdownDiffOutputBuilder'));

    $services->set(MarkdownDiffer::class)
        ->arg('$markdownDiffer', ref('markdownDiffer'));
};
