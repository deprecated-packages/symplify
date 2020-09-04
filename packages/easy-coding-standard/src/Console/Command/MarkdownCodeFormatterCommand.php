<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symplify\EasyCodingStandard\Configuration\Exception\NoMarkdownFileException;
use Symplify\EasyCodingStandard\Configuration\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MarkdownCodeFormatterCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('markdown-code-format');
        $this->setDescription('Format markdown code');
        $this->addArgument(
            'markdown-file',
            InputArgument::REQUIRED,
            'The markdown file'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $markdownFile = $input->getArgument('markdown-file');
        if (! file_exists($markdownFile)) {
            throw new NoMarkdownFileException(sprintf('Markdown file %s not found', $markdownFile));
        }

        return 0;
    }
}
