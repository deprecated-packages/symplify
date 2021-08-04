<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte\Analyzer;

use Nette\Utils\Strings;
use Symplify\EasyCI\Latte\Contract\LatteAnalyzerInterface;
use Symplify\EasyCI\Latte\ValueObject\LatteError;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\Latte\Analyzer\ForbiddenVariableConstantOrCallAnalyzer\ForbiddenVariableConstantOrCallAnalyzerTest
 */
final class ForbiddenVariableConstantOrCallAnalyzer implements LatteAnalyzerInterface
{
    /**
     * @var string
     */
    private const VARIABLE_PART_KEY = 'variable';

    /**
     * @var string
     */
    private const CONSTANT_OR_METHOD_PART_KEY = 'constant_or_method';

    /**
     * @var string
     * @see https://regex101.com/r/mDzFKI/4
     */
    private const ON_VARIABLE_CALL_REGEX = '#(?<'
        . self::VARIABLE_PART_KEY . '>\$[\w]+)::'
        . '(?<' . self::CONSTANT_OR_METHOD_PART_KEY . '>[\w_]+)#m';

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return LatteError[]
     */
    public function analyze(array $fileInfos): array
    {
        $latteErrors = [];
        foreach ($fileInfos as $fileInfo) {
            $currentLatteErrors = $this->analyzeFileInfo($fileInfo);
            $latteErrors = array_merge($latteErrors, $currentLatteErrors);
        }

        return $latteErrors;
    }

    /**
     * @return LatteError[]
     */
    private function analyzeFileInfo(SmartFileInfo $fileInfo): array
    {
        $matches = Strings::matchAll($fileInfo->getContents(), self::ON_VARIABLE_CALL_REGEX);

        $latteErrors = [];
        foreach ($matches as $match) {
            $errorMessage = sprintf(
                'On variable "%s::%s" call/constant fetch is not allowed',
                (string) $match[self::VARIABLE_PART_KEY],
                (string) $match[self::CONSTANT_OR_METHOD_PART_KEY],
            );

            $latteErrors[] = new LatteError($errorMessage, $fileInfo);
        }

        return $latteErrors;
    }
}
