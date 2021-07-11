<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Comments\CommentedCodeAnalyzer;
use Symplify\EasyCI\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ShellCode;

final class CheckCommentedCodeCommand extends AbstractSymplifyCommand
{
    /**
     * @var int
     */
    private const DEFAULT_LINE_LIMIT = 5;

    public function __construct(
        private CommentedCodeAnalyzer $commentedCodeAnalyzer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            Option::SOURCES,
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more paths to check'
        );
        $this->setDescription('Checks code for commented snippets');

        $this->addOption(
            Option::LINE_LIMIT,
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_OPTIONAL,
            'Amount of allowed comment lines in a row',
            self::DEFAULT_LINE_LIMIT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sources = (array) $input->getArgument(Option::SOURCES);
        $phpFileInfos = $this->smartFinder->find($sources, '*.php');

        $message = sprintf('Analysing %d *.php files', count($phpFileInfos));
        $this->symfonyStyle->note($message);

        $lineLimit = (int) $input->getOption(Option::LINE_LIMIT);

        $commentedLinesByFilePaths = [];
        foreach ($phpFileInfos as $phpFileInfo) {
            $commentedLines = $this->commentedCodeAnalyzer->process($phpFileInfo, $lineLimit);

            if ($commentedLines === []) {
                continue;
            }

            $commentedLinesByFilePaths[$phpFileInfo->getRelativeFilePathFromCwd()] = $commentedLines;
        }

        if ($commentedLinesByFilePaths === []) {
            $this->symfonyStyle->success('No comments found');
            return ShellCode::SUCCESS;
        }

        foreach ($commentedLinesByFilePaths as $filePath => $commentedLines) {
            foreach ($commentedLines as $commentedLine) {
                $this->symfonyStyle->writeln(' * ' . $filePath . ':' . $commentedLine);
            }
        }

        $this->symfonyStyle->error('Errors found');
        return ShellCode::ERROR;
    }
}
