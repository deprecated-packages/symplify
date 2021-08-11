<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand;
use Symplify\EasyCodingStandard\SnippetFormatter\Application\SnippetFormatterApplication;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;

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
        if (! $this->loadedCheckersGuard->areSomeCheckersRegistered()) {
            $this->loadedCheckersGuard->report();
            return self::FAILURE;
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
