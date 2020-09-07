<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Configuration\Exception\NoDirectoryException;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Heredoc\HeredocPHPCodeFormatter;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class CheckHeredocCommand extends Command
{
    /**
     * @var string
     */
    private const SOURCE = 'source';

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var HeredocPHPCodeFormatter
     */
    private $heredocPHPCodeFormatter;

    public function __construct(
        SmartFileSystem $smartFileSystem,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        HeredocPHPCodeFormatter $heredocPHPCodeFormatter
    ) {
        $this->smartFileSystem = $smartFileSystem;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;

        parent::__construct();

        $this->heredocPHPCodeFormatter = $heredocPHPCodeFormatter;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Format Heredoc PHP code');
        $this->addArgument(
            self::SOURCE,
            InputArgument::REQUIRED,
            'Path to the directory containing PHP Code with Heredoc inside'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $markdownFile */
        $markdownFile = $input->getArgument(self::SOURCE);
        if (! is_dir($markdownFile)) {
            $message = sprintf('Directory "%s" not found', $markdownFile);
            throw new NoDirectoryException($message);
        }

        $markdownFileInfo = new SmartFileInfo($markdownFile);
        $fixedContent = $this->heredocPHPCodeFormatter->format($markdownFileInfo);

        if ($markdownFileInfo->getContents() === $fixedContent) {
            $successMessage = 'PHP code in Heredoc already follow coding standard';
        } else {
            $this->smartFileSystem->dumpFile($markdownFile, (string) $fixedContent);
            $successMessage = 'PHP code in Heredoc has been fixed to follow coding standard';
        }

        $this->easyCodingStandardStyle->success($successMessage);

        return ShellCode::SUCCESS;
    }
}
