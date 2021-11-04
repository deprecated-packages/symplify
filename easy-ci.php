<?php

declare(strict_types=1);

use PHPStan\Rules\Rule;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use PhpCsFixer\Fixer\FixerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
use Symplify\EasyCI\Latte\Contract\LatteTemplateAnalyzerInterface;
use Symplify\EasyCI\Twig\Contract\TwigTemplateAnalyzerInterface;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\EasyCodingStandard\Tests\SniffRunner\Application\FixerSource\SomeFile;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\LattePHPStanCompiler\Contract\LatteVariableCollectorInterface;
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
use Symplify\Skipper\Contract\SkipVoterInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::EXCLUDED_CHECK_PATHS, ['Fixture', 'Source', 'tests', 'stubs', 'templates']);

    $parameters->set(Option::TYPES_TO_SKIP, [
        ConfigurableRuleInterface::class,
        Rule::class,
        MalformWorkerInterface::class,
        FixerInterface::class,
        BundleInterface::class,
        TestCase::class,
        Command::class,
        SetList::class,
        // part of tests
        SomeFile::class,
        Application::class,
        KernelInterface::class,
        TwigTemplateAnalyzerInterface::class,
        LatteTemplateAnalyzerInterface::class,
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
        ErrorFormatter::class,
        RoutingCaseConverterInterface::class,
        CategoryInfererInterface::class,
        DocumentedRuleInterface::class,
        CodeSampleInterface::class,
        LatteVariableCollectorInterface::class,
        TagResolverInterface::class,
    ]);
};
