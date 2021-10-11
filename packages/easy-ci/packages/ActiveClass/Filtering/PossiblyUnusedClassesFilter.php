<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\Filtering;

use PhpCsFixer\Fixer\FixerInterface;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use PHPStan\Rules\Rule;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
use Symplify\EasyCI\ActiveClass\ValueObject\FileWithClass;
use Symplify\EasyCI\Latte\Contract\LatteTemplateAnalyzerInterface;
use Symplify\EasyCI\Twig\Contract\TwigTemplateAnalyzerInterface;
use Symplify\EasyCodingStandard\Tests\SniffRunner\Application\FixerSource\SomeFile;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\EasyHydrator\Contract\TypeCasterInterface;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerJsonDecoratorInterface;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\Contract\RoutingCaseConverterInterface;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\RuleCodeSamplePrinterInterface;
use Symplify\Skipper\Contract\SkipVoterInterface;

final class PossiblyUnusedClassesFilter
{
    /**
     * @todo refactor to config passed parameter
     * @var class-string[]
     */
    private const EXCLUDED_TYPES = [
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
        TypeCasterInterface::class,
        CaseConverterInterface::class,
        ServiceOptionsKeyYamlToPhpFactoryInterface::class,
        DynamicMethodReturnTypeExtension::class,
        DynamicFunctionReturnTypeExtension::class,
        ErrorFormatter::class,
        RoutingCaseConverterInterface::class,
    ];

    /**
     * @param FileWithClass[] $filesWithClasses
     * @param string[] $usedNames
     * @return FileWithClass[]
     */
    public function filter(array $filesWithClasses, array $usedNames): array
    {
        $possiblyUnusedFilesWithClasses = [];

        foreach ($filesWithClasses as $fileWithClass) {
            if (in_array($fileWithClass->getClassName(), $usedNames, true)) {
                continue;
            }

            // is excluded interfaces?
            foreach (self::EXCLUDED_TYPES as $excludedType) {
                if (is_a($fileWithClass->getClassName(), $excludedType, true)) {
                    continue 2;
                }
            }

            $possiblyUnusedFilesWithClasses[] = $fileWithClass;
        }

        return $possiblyUnusedFilesWithClasses;
    }
}
