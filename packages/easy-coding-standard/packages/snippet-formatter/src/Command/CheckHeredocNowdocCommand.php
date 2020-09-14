<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class CheckHeredocNowdocCommand extends AbstractCheckCommand
{
    /**
     * @var Configuration
     */
    protected $configuration;

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
        Configuration $configuration,
        SmartFinder $smartFinder
    ) {
        $this->smartFileSystem = $smartFileSystem;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->configuration = $configuration;
        $this->snippetFormatter = $snippetFormatter;
        $this->smartFinder = $smartFinder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Format Heredoc/Nowdoc PHP snippets in PHP files');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configuration->resolveFromInput($input);

        $sources = $this->configuration->getSources();
        $phpFileInfos = $this->smartFinder->find($sources, '*.php');

        $fileCount = count($phpFileInfos);

        if ($fileCount > 0) {
            $this->easyCodingStandardStyle->progressStart($fileCount);

            foreach ($phpFileInfos as $phpFileInfo) {
                $this->processPHPFileInfo($phpFileInfo);
            }
        } else {
            return $this->printFileWarningAndExitSuccess($sources);
        }

        return $this->reportProcessedFiles($fileCount);
    }

    private function printFileWarningAndExitSuccess(array $sources): int
    {
        $warningMessage = sprintf(
            'No PHP files found in "%s" paths.%sCheck CLI arguments or "Option::PATHS" parameter in "ecs.php" config file',
            implode('", ', $sources),
            PHP_EOL
        );
        $this->easyCodingStandardStyle->warning($warningMessage);

        return ShellCode::SUCCESS;
    }

    private function processPHPFileInfo(SmartFileInfo $phpFileInfo): void
    {
        $fixedContent = $this->snippetFormatter->format($phpFileInfo, SnippetPattern::HERENOWDOC_SNIPPET_PATTERN);
        $this->easyCodingStandardStyle->progressAdvance();

        if ($phpFileInfo->getContents() === $fixedContent) {
            // nothing has changed
            return;
        }

        if ($this->configuration->isFixer()) {
            $this->smartFileSystem->dumpFile($phpFileInfo->getPathname(), (string) $fixedContent);
        }
    }
}
