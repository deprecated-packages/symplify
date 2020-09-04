<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MarkdownCodeFormatterCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('markdown-code-format');
        $this->setDescription('Format markdown code');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }
}
