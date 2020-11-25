<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Analyzer;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;
use function class_exists;
use function interface_exists;
use function trait_exists;

/**
 * @see \Symplify\TemplateChecker\Tests\Analyzer\MissingClassesLatteAnalyzer\MissingClassesLatteAnalyzerTest
 */
final class MissingClassesLatteAnalyzer
{
    /**
     * @see https://regex101.com/r/Wrfff2/7
     * @var string
     */
    private const CLASS_REGEX = '#\b(?<class>[A-Z][\w\\\\]+)::#m';

    /**
     * @see https://regex101.com/r/Wrfff2/12
     * @var string
     */
    private const VARTYPE_INSTANCEOF_CLASS_REGEX = '#(vartype|varType|instanceof|instanceOf)\s+(\\\\)?(?<class>[A-Z][\w\\\\]+)#ms';

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return string[]
     */
    public function analyze(array $fileInfos): array
    {
        $errors = [];

        foreach ($fileInfos as $fileInfo) {
            $classMatches = Strings::matchAll($fileInfo->getContents(), self::CLASS_REGEX);
            $varTypeInstanceOfClassMatches = Strings::matchAll(
                $fileInfo->getContents(),
                self::VARTYPE_INSTANCEOF_CLASS_REGEX
            );

            $matches = array_merge($classMatches, $varTypeInstanceOfClassMatches);
            if ($matches === []) {
                continue;
            }

            foreach ($matches as $foundClassesMatch) {
                $class = $foundClassesMatch['class'];
                if (class_exists($class) || trait_exists($class) || interface_exists($class)) {
                    continue;
                }

                $errors[] = sprintf(
                    'Class "%s" was not found in "%s"',
                    $class,
                    $fileInfo->getRelativeFilePathFromCwd()
                );
            }
        }

        return $errors;
    }
}
