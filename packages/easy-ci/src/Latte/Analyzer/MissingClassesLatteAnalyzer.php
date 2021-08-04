<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte\Analyzer;

use Nette\Utils\Strings;
use Symplify\EasyCI\Latte\Contract\LatteAnalyzerInterface;
use Symplify\EasyCI\Latte\ValueObject\LatteError;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\Latte\Analyzer\MissingClassesLatteAnalyzer\MissingClassesLatteAnalyzerTest
 */
final class MissingClassesLatteAnalyzer implements LatteAnalyzerInterface
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

    public function __construct(
        private ClassLikeExistenceChecker $classLikeExistenceChecker
    ) {
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return LatteError[]
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
                $class = (string) $foundClassesMatch['class'];
                if ($this->classLikeExistenceChecker->doesClassLikeExist($class)) {
                    continue;
                }

                $errors[] = new LatteError(sprintf('Class "%s" not found', $class), $fileInfo,);
            }
        }

        return $errors;
    }
}
