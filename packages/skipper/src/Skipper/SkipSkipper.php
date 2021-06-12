<?php

declare(strict_types=1);

namespace Symplify\Skipper\Skipper;

use Symplify\Skipper\Matcher\FileInfoMatcher;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\Skipper\Tests\Skipper\Skip\SkipSkipperTest
 */
final class SkipSkipper
{
    public function __construct(
        private FileInfoMatcher $fileInfoMatcher
    ) {
    }

    /**
     * @param array<string, string[]|null> $skippedClasses
     */
    public function doesMatchSkip(object | string $checker, SmartFileInfo $smartFileInfo, array $skippedClasses): bool
    {
        foreach ($skippedClasses as $skippedClass => $skippedFiles) {
            if (! is_a($checker, $skippedClass, true)) {
                continue;
            }

            // skip everywhere
            if (! is_array($skippedFiles)) {
                return true;
            }

            if ($this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $skippedFiles)) {
                return true;
            }
        }

        return false;
    }
}
