<?php

declare(strict_types=1);

namespace Symplify\ClassPresence\Regex;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\ClassPresence\Tests\Regex\NonExistingClassConstantExtractor\NonExistingClassConstantExtractorTest
 */
final class NonExistingClassConstantExtractor
{
    /**
     * @var string
     * @see https://regex101.com/r/Wrfff2/14
     * @see https://regex101.com/r/6ree2D/1
     */
    private const CLASS_CONSTANT_NAME_REGEX = '#(?<quote>["\']?)[\\\\]*\b(?<class_constant_name>[A-Z](\w+\\\\(\\\\)?)+(\w+)::[A-Z_0-9]+)#';

    /**
     * @return string[]
     */
    public function extractFromFileInfo(SmartFileInfo $fileInfo): array
    {
        $foundMatches = Strings::matchAll($fileInfo->getContents(), self::CLASS_CONSTANT_NAME_REGEX);
        if ($foundMatches === []) {
            return [];
        }

        $missingClassConstantNames = [];
        foreach ($foundMatches as $foundMatch) {
            $classConstantName = $foundMatch['class_constant_name'];

            if ($fileInfo->getSuffix() === 'twig' && $foundMatch['quote'] !== '') {
                $classConstantName = str_replace('\\\\', '\\', $classConstantName);
            }

            if (defined($classConstantName)) {
                continue;
            }

            $missingClassConstantNames[] = $classConstantName;
        }

        return $missingClassConstantNames;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return string[][]
     */
    public function extractFromFileInfos(array $fileInfos): array
    {
        $missingClassConstantsByFilePath = [];

        foreach ($fileInfos as $fileInfo) {
            $missingClassConstants = $this->extractFromFileInfo($fileInfo);
            if ($missingClassConstants === []) {
                continue;
            }

            $missingClassConstantsByFilePath[$fileInfo->getRelativePathname()] = $missingClassConstants;
        }

        return $missingClassConstantsByFilePath;
    }
}
