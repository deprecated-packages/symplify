<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Config\ConfigFileAnalyzer;

use Nette\Utils\Strings;
use Symplify\EasyCI\Config\Contract\ConfigFileAnalyzerInterface;
use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\EasyCI\ValueObject\FileError;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\Config\ConfigFileAnalyzer\NonExistingClassConstantExtractor\NonExistingClassConstantExtractorTest
 */
final class NonExistingClassConstantConfigFileAnalyzer implements ConfigFileAnalyzerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/Wrfff2/14
     * @see https://regex101.com/r/6ree2D/1
     */
    private const CLASS_CONSTANT_NAME_REGEX = '#(?<quote>["\']?)[\\\\]*\b(?<class_constant_name>[A-Z](\w+\\\\(\\\\)?)+(\w+)::[A-Z_0-9]+)#';

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function processFileInfos(array $fileInfos): array
    {
        $fileErrors = [];

        foreach ($fileInfos as $fileInfo) {
            $missingClassConstants = $this->extractFromFileInfo($fileInfo);

            foreach ($missingClassConstants as $missingClassConstant) {
                $errorMessage = sprintf('Class constant "%s" does not exist', $missingClassConstant);
                $fileErrors[] = new FileError($errorMessage, $fileInfo);
            }
        }

        return $fileErrors;
    }

    /**
     * @return string[]
     */
    private function extractFromFileInfo(SmartFileInfo $fileInfo): array
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
}
