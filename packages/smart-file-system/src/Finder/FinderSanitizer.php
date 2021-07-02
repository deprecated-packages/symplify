<?php

declare(strict_types=1);

namespace Symplify\SmartFileSystem\Finder;

use Nette\Utils\Finder as NetteFinder;
use SplFileInfo;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\SmartFileSystem\Tests\Finder\FinderSanitizer\FinderSanitizerTest
 */
final class FinderSanitizer
{
    /**
     * @param NetteFinder|SymfonyFinder|SplFileInfo[]|SymfonySplFileInfo[]|string[] $files
     * @return SmartFileInfo[]
     */
    public function sanitize(NetteFinder | SymfonyFinder | array $files): array
    {
        $smartFileInfos = [];
        foreach ($files as $file) {
            $fileInfo = is_string($file) ? new SplFileInfo($file) : $file;
            if (! $this->isFileInfoValid($fileInfo)) {
                continue;
            }

            /** @var string $realPath */
            $realPath = $fileInfo->getRealPath();

            $smartFileInfos[] = new SmartFileInfo($realPath);
        }

        return $smartFileInfos;
    }

    private function isFileInfoValid(SplFileInfo $fileInfo): bool
    {
        return (bool) $fileInfo->getRealPath();
    }
}
