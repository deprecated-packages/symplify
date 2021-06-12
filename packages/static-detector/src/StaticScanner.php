<?php

declare(strict_types=1);

namespace Symplify\StaticDetector;

use PhpParser\Parser;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\StaticDetector\CurrentProvider\CurrentFileInfoProvider;
use Symplify\StaticDetector\NodeTraverser\StaticCollectNodeTraverser;

/**
 * @see \Symplify\StaticDetector\Tests\StaticScanner\StaticScannerTest
 */
final class StaticScanner
{
    public function __construct(
        private StaticCollectNodeTraverser $staticCollectNodeTraverser,
        private Parser $parser,
        private SymfonyStyle $symfonyStyle,
        private CurrentFileInfoProvider $currentFileInfoProvider
    ) {
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     */
    public function scanFileInfos(array $fileInfos): void
    {
        $this->symfonyStyle->note('Looking for static methods and their calls...');

        $stepCount = count($fileInfos);
        $this->symfonyStyle->progressStart($stepCount);

        foreach ($fileInfos as $fileInfo) {
            $this->currentFileInfoProvider->setCurrentFileInfo($fileInfo);

            $processingMessage = sprintf('Processing "%s" file', $fileInfo->getRelativeFilePathFromCwd());

            if ($this->symfonyStyle->isDebug()) {
                $this->symfonyStyle->note($processingMessage);
            } else {
                $this->symfonyStyle->progressAdvance();
            }

            // collect static calls
            // collect static class methods
            $this->scanFileInfo($fileInfo);
        }

        $this->symfonyStyle->newLine(2);
        $this->symfonyStyle->success('Scanning done');
        $this->symfonyStyle->newLine(1);
    }

    private function scanFileInfo(SmartFileInfo $smartFileInfo): void
    {
        $nodes = $this->parser->parse($smartFileInfo->getContents());
        if ($nodes === null) {
            return;
        }

        $this->staticCollectNodeTraverser->traverse($nodes);
    }
}
