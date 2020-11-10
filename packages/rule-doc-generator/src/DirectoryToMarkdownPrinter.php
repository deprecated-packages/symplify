<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\Finder\ClassByTypeFinder;
use Symplify\RuleDocGenerator\Printer\RuleDefinitionsPrinter;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\DirectoryToMarkdownPrinterTest
 */
final class DirectoryToMarkdownPrinter
{
    /**
     * @var ClassByTypeFinder
     */
    private $classByTypeFinder;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var RuleDefinitionsResolver
     */
    private $ruleDefinitionsResolver;

    /**
     * @var RuleDefinitionsPrinter
     */
    private $ruleDefinitionsPrinter;

    public function __construct(
        ClassByTypeFinder $classByTypeFinder,
        SymfonyStyle $symfonyStyle,
        RuleDefinitionsResolver $ruleDefinitionsResolver,
        RuleDefinitionsPrinter $ruleDefinitionsPrinter
    ) {
        $this->classByTypeFinder = $classByTypeFinder;
        $this->symfonyStyle = $symfonyStyle;
        $this->ruleDefinitionsResolver = $ruleDefinitionsResolver;
        $this->ruleDefinitionsPrinter = $ruleDefinitionsPrinter;
    }

    public function printDirectory(SmartFileInfo $directoryFileInfo): string
    {
        // 1. collect documented rules in provided path
        $documentedRuleClasses = $this->classByTypeFinder->findByType(
            $directoryFileInfo,
            DocumentedRuleInterface::class
        );

        $message = sprintf('Found %d documented rule classes', count($documentedRuleClasses));
        $this->symfonyStyle->note($message);

        $this->symfonyStyle->listing($documentedRuleClasses);

        // 2. create rule definition collection
        $ruleDefinitions = $this->ruleDefinitionsResolver->resolveFromClassNames($documentedRuleClasses);

        // 3. print rule definitions to markdown lines
        $markdownLines = $this->ruleDefinitionsPrinter->print($ruleDefinitions);

        $fileContent = '';
        foreach ($markdownLines as $markdownLine) {
            $fileContent .= trim($markdownLine) . PHP_EOL . PHP_EOL;
        }

        return rtrim($fileContent) . PHP_EOL;
    }
}
