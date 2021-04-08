<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\RuleDocGenerator\DirectoryToMarkdownPrinter;
use Symplify\RuleDocGenerator\ValueObject\Option;
use Symplify\SmartFileSystem\SmartFileInfo;

final class GenerateCommand extends AbstractSymplifyCommand
{
    /**
     * @var DirectoryToMarkdownPrinter
     */
    private $directoryToMarkdownPrinter;

    public function __construct(DirectoryToMarkdownPrinter $directoryToMarkdownPrinter)
    {
        parent::__construct();

        $this->directoryToMarkdownPrinter = $directoryToMarkdownPrinter;
    }

    protected function configure(): void
    {
        $this->setDescription('Generated Markdown documentation based on documented rules found in directory');
        $this->addArgument(
            Option::PATHS,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Path to directory of your project'
        );
        $this->addOption(
            Option::OUTPUT_FILE,
            null,
            InputOption::VALUE_REQUIRED,
            'Path to output generated markdown file',
            getcwd() . '/docs/rules_overview.md'
        );
        $this->addOption(Option::CATEGORIZE, null, InputOption::VALUE_NONE, 'Group in categories');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paths = (array) $input->getArgument(Option::PATHS);
        $shouldCategorize = (bool) $input->getOption(Option::CATEGORIZE);

        // dump markdown file
        $outputFilePath = (string) $input->getOption(Option::OUTPUT_FILE);

        $markdownFileDirectory = dirname($outputFilePath);

        // ensure directory exists
        if (! $this->smartFileSystem->exists($markdownFileDirectory)) {
            $this->smartFileSystem->mkdir($markdownFileDirectory);
        }

        $markdownFileContent = $this->directoryToMarkdownPrinter->print(
            $markdownFileDirectory,
            $paths,
            $shouldCategorize
        );

        $this->smartFileSystem->dumpFile($outputFilePath, $markdownFileContent);

        $outputFileInfo = new SmartFileInfo($outputFilePath);
        $message = sprintf('File "%s" was created', $outputFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
