<?php

declare(strict_types=1);

use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
use Symplify\EasyCI\Config\EasyCIConfig;
use Symplify\EasyCI\Latte\Contract\LatteTemplateAnalyzerInterface;
use Symplify\EasyCI\Twig\Contract\TwigTemplateAnalyzerInterface;
use Symplify\EasyCodingStandard\Tests\SniffRunner\Application\FixerSource\SomeFile;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\MonorepoBuilder\Contract\Git\TagResolverInterface;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerJsonDecoratorInterface;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\Contract\RoutingCaseConverterInterface;
use Symplify\RuleDocGenerator\Contract\Category\CategoryInfererInterface;
use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Symplify\EasyCodingStandard\Skipper\Contract\SkipVoterInterface;

return static function (EasyCIConfig $easyCIConfig): void {
    $easyCIConfig->excludeCheckPaths(['Fixture', 'Source', 'tests', 'stubs', 'templates']);

    $easyCIConfig->typesToSkip([
        // @todo remove in next PR
        \PHPStan\Collectors\Collector::class,

        'SomeClass',
        ConfigurableRuleInterface::class,
        MalformWorkerInterface::class,
        SetList::class,
        // part of tests
        SomeFile::class,
        Application::class,
        TwigTemplateAnalyzerInterface::class,
        CompilerPassInterface::class,
        ReleaseWorkerInterface::class,
        ComposerKeyMergerInterface::class,
        ComposerJsonDecoratorInterface::class,
        RuleCodeSamplePrinterInterface::class,
        SkipVoterInterface::class,
        CaseConverterInterface::class,
        ServiceOptionsKeyYamlToPhpFactoryInterface::class,
        DynamicMethodReturnTypeExtension::class,
        DynamicFunctionReturnTypeExtension::class,
        RoutingCaseConverterInterface::class,
        CategoryInfererInterface::class,
        DocumentedRuleInterface::class,
        CodeSampleInterface::class,
        TagResolverInterface::class,
        \Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator::class,
        \PhpParser\NodeVisitorAbstract::class,
    ]);
};
