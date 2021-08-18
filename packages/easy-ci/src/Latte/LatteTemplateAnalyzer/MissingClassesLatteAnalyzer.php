<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte\LatteTemplateAnalyzer;

use Nette\Utils\Strings;
use Symplify\EasyCI\Latte\Contract\LatteTemplateAnalyzerInterface;
use Symplify\EasyCI\ValueObject\TemplateError;
use Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\Latte\LatteTemplateAnalyzer\MissingClassesLatteAnalyzer\MissingClassesLatteAnalyzerTest
 */
final class MissingClassesLatteAnalyzer implements LatteTemplateAnalyzerInterface
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
     * @return TemplateError[]
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

                $errors[] = new TemplateError(sprintf('Class "%s" not found', $class), $fileInfo,);
            }
        }

        return $errors;
    }
}
