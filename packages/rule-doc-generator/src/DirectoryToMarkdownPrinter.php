<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\Contract\RuleOutFilterInterface;
use Symplify\RuleDocGenerator\Finder\ClassByTypeFinder;
use Symplify\RuleDocGenerator\Printer\RuleDefinitionsPrinter;
use Symplify\RuleDocGenerator\ValueObject\RuleClassWithFilePath;

/**
 * @see \Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\DirectoryToMarkdownPrinterTest
 */
final class DirectoryToMarkdownPrinter
{
    /**
     * @param RuleOutFilterInterface[] $ruleOutFilters
     */
    public function __construct(
        private ClassByTypeFinder $classByTypeFinder,
        private SymfonyStyle $symfonyStyle,
        private RuleDefinitionsResolver $ruleDefinitionsResolver,
        private RuleDefinitionsPrinter $ruleDefinitionsPrinter,
        private array $ruleOutFilters
    ) {
    }

    /**
     * @param string[] $directories
     */
    public function print(string $workingDirectory, array $directories, bool $shouldCategorize): string
    {
        // 1. collect documented rules in provided path
        $documentedRuleClasses = $this->classByTypeFinder->findByType(
            $workingDirectory,
            $directories,
            DocumentedRuleInterface::class
        );

        // apply user-defined filters to remove some rules
        foreach ($this->ruleOutFilters as $ruleOutFilter) {
            $documentedRuleClasses = $ruleOutFilter->filter($documentedRuleClasses);
        }

        $message = sprintf('Found %d documented rule classes', count($documentedRuleClasses));
        $this->symfonyStyle->note($message);

        $classes = array_map(
            static fn (RuleClassWithFilePath $ruleClassWithFilePath): string => $ruleClassWithFilePath->getClass(),
            $documentedRuleClasses
        );

        $this->symfonyStyle->listing($classes);

        // 2. create rule definition collection
        $this->symfonyStyle->note('Resolving rule definitions');

        $ruleDefinitions = $this->ruleDefinitionsResolver->resolveFromClassNames($documentedRuleClasses);

        // 3. print rule definitions to markdown lines
        $this->symfonyStyle->note('Printing rule definitions');
        $markdownLines = $this->ruleDefinitionsPrinter->print($ruleDefinitions, $shouldCategorize);

        $fileContent = '';
        foreach ($markdownLines as $markdownLine) {
            $fileContent .= trim($markdownLine) . PHP_EOL . PHP_EOL;
        }

        return rtrim($fileContent) . PHP_EOL;
    }
}
