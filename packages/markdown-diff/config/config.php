<?php

declare(strict_types=1);

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MarkdownDiff\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use Symplify\MarkdownDiff\Differ\MarkdownDiffer;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\MarkdownDiff\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Bundle']);

    $services->set(Differ::class);

    // markdown
    $services->set('markdownDiffOutputBuilder', UnifiedDiffOutputBuilder::class)
        ->factory([service(CompleteUnifiedDiffOutputBuilderFactory::class), 'create']);

    $services->set('markdownDiffer', Differ::class)
        ->arg('$outputBuilder', service('markdownDiffOutputBuilder'));

    $services->set(MarkdownDiffer::class)
        ->arg('$markdownDiffer', service('markdownDiffer'));

    $services->set(PrivatesAccessor::class);
};
