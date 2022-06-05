<?php

declare(strict_types=1);

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\RuleDocGenerator\Command\GenerateCommand;
use Symplify\RuleDocGenerator\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use Symplify\RuleDocGenerator\MarkdownDiffer\MarkdownDiffer;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire();

    $services->load('Symplify\RuleDocGenerator\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);

    $services->set(Application::class)
        ->call('add', [service(GenerateCommand::class)]);

    $services->set(ClassLikeExistenceChecker::class);
    $services->set(AsciiSlugger::class);

    $services->set(Differ::class);

    // markdown
    $services->set('markdownDiffOutputBuilder', UnifiedDiffOutputBuilder::class)
        ->factory([service(CompleteUnifiedDiffOutputBuilderFactory::class), 'create']);

    $services->set('markdownDiffer', Differ::class)
        ->arg('$outputBuilder', service('markdownDiffOutputBuilder'));

    $services->set(MarkdownDiffer::class)
        ->arg('$differ', service('markdownDiffer'));

    $services->set(PrivatesAccessor::class);
};
