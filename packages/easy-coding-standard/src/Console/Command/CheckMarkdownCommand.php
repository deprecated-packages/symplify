<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Configuration\Exception\NoMarkdownFileException;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\Console\Output\OutputFormatterCollector;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Markdown\MarkdownPHPCodeFormatter;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class CheckMarkdownCommand extends Command
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
     * @var MarkdownPHPCodeFormatter
     */
    private $markdownPHPCodeFormatter;

    /**
     * @var OutputFormatterCollector
     */
    private $outputFormatterCollector;
    /**
     * @var SmartFinder
     */
    private $smartFinder;

    public function __construct(
        SmartFileSystem $smartFileSystem,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        MarkdownPHPCodeFormatter $markdownPHPCodeFormatter,
        OutputFormatterCollector $outputFormatterCollector,
        Configuration $configuration,
        SmartFinder $smartFinder
    ) {
        $this->smartFileSystem = $smartFileSystem;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->markdownPHPCodeFormatter = $markdownPHPCodeFormatter;
        $this->outputFormatterCollector = $outputFormatterCollector;
        $this->configuration = $configuration;
        $this->smartFinder = $smartFinder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Format Markdown PHP code');
        $this->addArgument(Option::SOURCE, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to the Markdown file or directories to scan');
        $this->addOption(Option::NO_STRICT_TYPES_DECLARATION, null, null, 'No strict types declaration');
        $this->addOption(Option::FIX, null, null, 'Fix found violations.');

        $this->addOption(
            Option::OUTPUT_FORMAT,
            null,
            InputOption::VALUE_REQUIRED,
            'Select output format',
            ConsoleOutputFormatter::NAME
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument(Option::SOURCE);
        $markdownFileInfos = $this->smartFinder->find($sources, '*.md');

        $noStrictTypesDeclaration = (bool) $input->getOption(Option::NO_STRICT_TYPES_DECLARATION);
        $fix = (bool) $input->getOption(Option::FIX);

        foreach ($markdownFileInfos as $markdownFileInfo) {
            $this->processMarkdownFileInfo($markdownFileInfo, $noStrictTypesDeclaration, $fix);
        }

        return ShellCode::SUCCESS;
    }

    private function resolveOutputFormat(InputInterface $input): string
    {
        $outputFormat = (string) $input->getOption(Option::OUTPUT_FORMAT);

        // Backwards compatibility with older version
        if ($outputFormat === 'table') {
            return ConsoleOutputFormatter::NAME;
        }

        return $outputFormat;
    }

    private function processMarkdownFileInfo(SmartFileInfo $markdownFileInfo, bool $noStrictTypesDeclaration, bool $fix): void
    {
        $fixedContent = $this->markdownPHPCodeFormatter->format($markdownFileInfo, $noStrictTypesDeclaration);

        if ($markdownFileInfo->getContents() === $fixedContent) {
            $successMessage = sprintf(
                'PHP code in "%s" already follows coding standard',
                $markdownFileInfo->getRelativeFilePathFromCwd()
            );

            $this->easyCodingStandardStyle->note($successMessage);

        } elseif ($fix) {
            $this->smartFileSystem->dumpFile($markdownFileInfo->getPathname(), (string) $fixedContent);
            $successMessage = 'PHP code in Markdown has been fixed to follow coding standard';
        } else {
            $this->configuration->disableFixing();

            $outputFormat = $this->resolveOutputFormat($input);
            /** @var ConsoleOutputFormatter $outputFormatter */
            $outputFormatter = $this->outputFormatterCollector->getByName($outputFormat);
            $outputFormatter->disableHeaderFileDiff();

            return $outputFormatter->report(1);
        }

        $this->easyCodingStandardStyle->success($successMessage);
    }
}
