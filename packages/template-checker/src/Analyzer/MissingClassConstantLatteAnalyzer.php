<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Analyzer;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;
use function defined;

/**
 * @see \Symplify\TemplateChecker\Tests\Analyzer\MissingClassConstantLatteAnalyzer\MissingClassConstantLatteAnalyzerTest
 */
final class MissingClassConstantLatteAnalyzer
{
    /**
     * @see https://regex101.com/r/Wrfff2/9
     * @var string
     */
    private const CLASS_CONSTANT_REGEX = '#\b(?<class_constant_name>[A-Z][\w\\\\]+::[A-Z_]+)\b#m';

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return string[]
     */
    public function analyze(array $fileInfos): array
    {
        $errors = [];

        foreach ($fileInfos as $fileInfo) {
            $matches = Strings::matchAll($fileInfo->getContents(), self::CLASS_CONSTANT_REGEX);
            if ($matches === []) {
                continue;
            }

            foreach ($matches as $foundMatch) {
                $classConstantName = $foundMatch['class_constant_name'];
                if (defined($classConstantName)) {
                    continue;
                }

                $errors[] = sprintf(
                    'Class constant "%s" was not found in "%s"',
                    $classConstantName,
                    $fileInfo->getRelativeFilePathFromCwd()
                );
            }
        }

        return $errors;
    }
}
