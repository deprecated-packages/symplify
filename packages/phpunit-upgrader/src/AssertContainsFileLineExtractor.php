<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader;

use Nette\Utils\Strings;
use Symplify\PHPUnitUpgrader\ValueObject\FileLine;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AssertContainsFileLineExtractor
{
    /**
     * @var string
     * @see https://regex101.com/r/FJV3hp/4
     */
    private const FILE_PATH_AND_LINE_REGEX = '#string given, called in (?<file_path>[\w\/\.\-]+) on line (?<line>\d+)#';

    /**
     * @return FileLine[]
     */
    public function extract(SmartFileInfo $fileInfo): array
    {
        $fileLines = [];

        $matches = Strings::matchAll($fileInfo->getContents(), self::FILE_PATH_AND_LINE_REGEX);
        foreach ($matches as $match) {
            $fileLines[] = new FileLine($match['file_path'], $match['line'] - 1);
        }

        return $fileLines;
    }
}
