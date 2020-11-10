<?php

declare(strict_types=1);

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MarkdownDiff\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use Symplify\MarkdownDiff\Differ\MarkdownDiffer;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\MarkdownDiff\\', __DIR__ . '/../src');

    $services->set(Differ::class);

    // markdown
    $services->set('markdownDiffOutputBuilder', UnifiedDiffOutputBuilder::class)
        ->factory([ref(CompleteUnifiedDiffOutputBuilderFactory::class), 'create']);

    $services->set('markdownDiffer', Differ::class)
        ->arg('$outputBuilder', ref('markdownDiffOutputBuilder'));

    $services->set(MarkdownDiffer::class)
        ->arg('$markdownDiffer', ref('markdownDiffer'));
};
