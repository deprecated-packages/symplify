<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

use Symplify\Skipper\Matcher\FileInfoMatcher;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SkipNextReturnDifferentMethodCall
{
    public function __construct(
        private FileInfoMatcher $fileInfoMatcher
    ) {
    }

    public function run($onlyFiles, SmartFileInfo $smartFileInfo)
    {
        if ($onlyFiles === null) {
            return true;
        }

        return ! $this->fileInfoMatcher->doesFileInfoMatchPatterns($smartFileInfo, $onlyFiles);
    }
}
