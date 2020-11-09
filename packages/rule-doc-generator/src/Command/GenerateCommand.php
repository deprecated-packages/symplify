<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\RuleDocGenerator\DirectoryToMarkdownPrinter;
use Symplify\RuleDocGenerator\ValueObject\Option;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class GenerateCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var DirectoryToMarkdownPrinter
     */
    private $directoryToMarkdownPrinter;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        SmartFileSystem $smartFileSystem,
        DirectoryToMarkdownPrinter $directoryToMarkdownPrinter
    ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
        $this->directoryToMarkdownPrinter = $directoryToMarkdownPrinter;
    }

    protected function configure(): void
    {
        $this->setDescription('Generated Markdown documentation based on documented rules found in directory');
        $this->addArgument(Option::PATH, InputArgument::REQUIRED, 'Path to directory of your project');
        $this->addOption(
            Option::OUTPUT,
            null,
            InputOption::VALUE_REQUIRED,
            'Path to output generated markdown file',
            getcwd() . '/docs/rules_overview.md'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument(Option::PATH);

        $directoryFileInfo = new SmartFileInfo($path);
        $markdownFileContent = $this->directoryToMarkdownPrinter->printDirectory($directoryFileInfo);

        // dump markdown file
        $outputFilePath = (string) $input->getOption(Option::OUTPUT);
        $this->smartFileSystem->dumpFile($outputFilePath, $markdownFileContent);

        $outputFileInfo = new SmartFileInfo($outputFilePath);
        $message = sprintf('File "%s" was created', $outputFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
