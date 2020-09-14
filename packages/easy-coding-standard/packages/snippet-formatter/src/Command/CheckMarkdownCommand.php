<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

/**
 * @todo refactor to generic parent AbstractSnippetFormatterCommand
 */
final class CheckMarkdownCommand extends AbstractCheckCommand
{
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var SnippetFormatter
     */
    private $snippetFormatter;

    /**
     * @var SmartFinder
     */
    private $smartFinder;

    public function __construct(
        SmartFileSystem $smartFileSystem,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        SnippetFormatter $snippetFormatter,
        SmartFinder $smartFinder
    ) {
        $this->smartFileSystem = $smartFileSystem;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->snippetFormatter = $snippetFormatter;
        $this->smartFinder = $smartFinder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Format Markdown PHP code');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configuration->resolveFromInput($input);

        $sources = $this->configuration->getSources();
        $markdownFileInfos = $this->smartFinder->find($sources, '*.md');

        $fileCount = count($markdownFileInfos);
        if ($fileCount > 0) {
            $this->easyCodingStandardStyle->progressStart($fileCount);

            foreach ($markdownFileInfos as $markdownFileInfo) {
                $this->processMarkdownFileInfo($markdownFileInfo);
            }
        } else {
            return $this->printNoFilesWarningAndExistSuccess($sources);
        }

        return $this->reportProcessedFiles($fileCount);
    }

    private function processMarkdownFileInfo(SmartFileInfo $markdownFileInfo): void
    {
        $fixedContent = $this->snippetFormatter->format(
            $markdownFileInfo,
            SnippetPattern::MARKDOWN_PHP_SNIPPET_PATTERN
        );
        $this->easyCodingStandardStyle->progressAdvance();

        if ($markdownFileInfo->getContents() === $fixedContent) {
            // nothing has changed
            return;
        }

        if ($this->configuration->isFixer()) {
            $this->smartFileSystem->dumpFile($markdownFileInfo->getPathname(), (string) $fixedContent);
        }
    }

    private function printNoFilesWarningAndExistSuccess(array $sources): int
    {
        $warningMessage = sprintf(
            'No Markdown files found in "%s" paths.%sCheck CLI arguments or "Option::PATHS" parameter in "ecs.php" config file',
            implode('", ', $sources),
            PHP_EOL
        );
        $this->easyCodingStandardStyle->warning($warningMessage);

        return ShellCode::SUCCESS;
    }
}
