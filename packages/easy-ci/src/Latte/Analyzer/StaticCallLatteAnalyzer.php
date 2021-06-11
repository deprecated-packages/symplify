<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte\Analyzer;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Symplify\EasyCI\Latte\Contract\LatteAnalyzerInterface;
use Symplify\EasyCI\Latte\ValueObject\LatteError;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\Latte\LatteStaticCallAnalyzer\LatteStaticCallAnalyzerTest
 */
final class StaticCallLatteAnalyzer implements LatteAnalyzerInterface
{
    /**
     * @var string
     */
    private const CLASS_NAME_PART = 'class';

    /**
     * @var string
     */
    private const METHOD_NAME_PART = 'method';

    /**
     * @var string
     * @see https://regex101.com/r/mDzFKI/4
     */
    private const STATIC_CALL_REGEX = '#(?<' .
        self::CLASS_NAME_PART . '>(\$|\b[A-Z])[\w\\\\]+)::(?<' .
        self::METHOD_NAME_PART . '>[\w]+)\((.*?)?\)#m';

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return LatteError[]
     */
    public function analyze(array $fileInfos): array
    {
        $classMethodNames = [];
        foreach ($fileInfos as $fileInfo) {
            $fileClassMethodNames = $this->analyzeFileInfo($fileInfo);
            $classMethodNames = array_merge($classMethodNames, $fileClassMethodNames);
        }

        return $classMethodNames;
    }

    /**
     * @return LatteError[]
     */
    private function analyzeFileInfo(SmartFileInfo $fileInfo): array
    {
        $matches = Strings::matchAll($fileInfo->getContents(), self::STATIC_CALL_REGEX);
        $matches = $this->filterOutAllowedStaticClasses($matches);

        $latteErrors = [];
        foreach ($matches as $match) {
            $errorMessage = sprintf(
                'Method "%s::%s()" was not found',
                $match[self::CLASS_NAME_PART],
                $match[self::METHOD_NAME_PART]
            );

            $latteErrors[] = new LatteError($errorMessage, $fileInfo);
        }

        return $latteErrors;
    }

    /**
     * @param string[][] $matches
     * @return string[][]
     */
    private function filterOutAllowedStaticClasses(array $matches): array
    {
        return array_filter($matches, static function (array $match): bool {
            return ! in_array($match[self::CLASS_NAME_PART], [Strings::class, DateTime::class], true);
        });
    }
}
