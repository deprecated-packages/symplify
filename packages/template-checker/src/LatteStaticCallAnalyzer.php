<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\TemplateChecker\ValueObject\ClassMethodName;

/**
 * @see \Symplify\TemplateChecker\Tests\LatteStaticCallAnalyzer\LatteStaticCallAnalyzerTest
 */
final class LatteStaticCallAnalyzer
{
    /**
     * @var string
     * @see https://regex101.com/r/mDzFKI/4
     */
    private const STATIC_CALL_REGEX = '#(?<class>(\$|\b[A-Z])[\w\\\\]+)::(?<method>[\w]+)\((.*?)?\)#m';

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return ClassMethodName[]
     */
    public function analyzeFileInfos(array $fileInfos): array
    {
        $classMethodNames = [];
        foreach ($fileInfos as $fileInfo) {
            $fileClassMethodNames = $this->analyzeFileInfo($fileInfo);
            $classMethodNames = array_merge($classMethodNames, $fileClassMethodNames);
        }

        return $classMethodNames;
    }

    /**
     * @return ClassMethodName[]
     */
    private function analyzeFileInfo(SmartFileInfo $fileInfo): array
    {
        $matches = Strings::matchAll($fileInfo->getContents(), self::STATIC_CALL_REGEX);
        $matches = $this->filterOutAllowedStaticClasses($matches);

        $classMethodNames = [];
        foreach ($matches as $match) {
            $classMethodNames[] = new ClassMethodName($match['class'], $match['method'], $fileInfo);
        }

        return $classMethodNames;
    }

    /**
     * @param mixed[] $matches
     * @return mixed[]
     */
    private function filterOutAllowedStaticClasses(array $matches): array
    {
        return array_filter($matches, static function (array $match): bool {
            return ! in_array($match['class'], [Strings::class, DateTime::class], true);
        });
    }
}
