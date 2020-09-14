<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Markdown\MarkdownPHPCodeFormatter;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

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
     * @var MarkdownPHPCodeFormatter
     */
    private $markdownPHPCodeFormatter;

    /**
     * @var SmartFinder
     */
    private $smartFinder;

    public function __construct(
        SmartFileSystem $smartFileSystem,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        MarkdownPHPCodeFormatter $markdownPHPCodeFormatter,
        SmartFinder $smartFinder
    ) {
        $this->smartFileSystem = $smartFileSystem;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->markdownPHPCodeFormatter = $markdownPHPCodeFormatter;
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
        /** @var string[] $sources */
        $sources = (array) $input->getArgument(Option::SOURCE);
        $markdownFileInfos = $this->smartFinder->find($sources, '*.md');

        $this->configuration->resolveFromInput($input);
        $this->configureMarkdown();

        $fileCount = count($markdownFileInfos);
        $this->easyCodingStandardStyle->progressStart($fileCount);

        foreach ($markdownFileInfos as $markdownFileInfo) {
            $this->processMarkdownFileInfo($markdownFileInfo);
        }

        return $this->reportProcessedFiles($fileCount);
    }

    private function processMarkdownFileInfo(SmartFileInfo $markdownFileInfo): void
    {
        $fixedContent = $this->markdownPHPCodeFormatter->format($markdownFileInfo);
        if ($markdownFileInfo->getContents() === $fixedContent) {
            // nothing has changed
            return;
        }

        if ($this->configuration->isFixer()) {
            $this->smartFileSystem->dumpFile($markdownFileInfo->getPathname(), (string) $fixedContent);
        }

        $this->easyCodingStandardStyle->progressAdvance();
    }
}
