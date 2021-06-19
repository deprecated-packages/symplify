<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand;
use Symplify\EasyCodingStandard\SnippetFormatter\Application\SnippetFormatterApplication;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;
use Symplify\PackageBuilder\Console\ShellCode;

final class CheckHeredocNowdocCommand extends AbstractCheckCommand
{
    public function __construct(
        private SnippetFormatterApplication $snippetFormatterApplication,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Format Heredoc/Nowdoc PHP snippets in PHP files');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! $this->loadedCheckersGuard->areSomeCheckerRegistered()) {
            $this->loadedCheckersGuard->report();
            return ShellCode::ERROR;
        }

        $configuration = $this->configurationFactory->createFromInput($input);
        $phpFileInfos = $this->smartFinder->find($configuration->getSources(), '*.php', ['Fixture']);

        return $this->snippetFormatterApplication->processFileInfosWithSnippetPattern(
            $configuration,
            $phpFileInfos,
            SnippetPattern::HERENOWDOC_SNIPPET_REGEX,
            'heredocnowdox'
        );
    }
}
