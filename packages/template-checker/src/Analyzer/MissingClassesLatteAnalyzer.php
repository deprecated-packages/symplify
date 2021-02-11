<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Analyzer;

use Nette\Utils\Strings;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use Symplify\SmartFileSystem\SmartFileInfo;

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
     * @var ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;

    public function __construct(ClassLikeExistenceChecker $classLikeExistenceChecker)
    {
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;
    }

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
                if ($this->classLikeExistenceChecker->doesClassLikeExist($class)) {
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
