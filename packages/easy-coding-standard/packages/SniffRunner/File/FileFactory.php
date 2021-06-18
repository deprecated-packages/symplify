<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use PHP_CodeSniffer\Fixer;
use Symplify\EasyCodingStandard\Application\AppliedCheckersCollector;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\Skipper\Skipper\Skipper;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCodingStandard\Tests\SniffRunner\File\FileFactoryTest
 */
final class FileFactory
{
    public function __construct(
        private Fixer $fixer,
        private Skipper $skipper,
        private AppliedCheckersCollector $appliedCheckersCollector,
        private EasyCodingStandardStyle $easyCodingStandardStyle
    ) {
    }

    public function createFromFileInfo(SmartFileInfo $smartFileInfo): File
    {
        return new File(
            $smartFileInfo->getRelativeFilePath(),
            $smartFileInfo->getContents(),
            $this->fixer,
            $this->skipper,
            $this->appliedCheckersCollector,
            $this->easyCodingStandardStyle
        );
    }
}
