<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte\Analyzer;

use Nette\Utils\Strings;
use Symplify\EasyCI\Latte\Contract\LatteAnalyzerInterface;
use Symplify\EasyCI\Latte\ValueObject\LatteError;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\Analyzer\MissingClassStaticCallLatteAnalyzer\MissingClassStaticCallLatteAnalyzerTest
 */
final class MissingClassStaticCallLatteAnalyzer implements LatteAnalyzerInterface
{
    /**
     * @var string
     */
    private const CLASS_KEY_PART = 'class';

    /**
     * @var string
     */
    private const METHOD_KEY_PART = 'method';

    /**
     * @see https://regex101.com/r/Wrfff2/8
     * @var string
     */
    private const CLASS_STATIC_CALL_REGEX = '#\b(?<' .
        self::CLASS_KEY_PART . '>[A-Z][\w\\\\]+)::(?<' .
        self::METHOD_KEY_PART . '>\w+)\(#m';

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return LatteError[]
     */
    public function analyze(array $fileInfos): array
    {
        $latteErrors = [];

        foreach ($fileInfos as $fileInfo) {
            $matches = Strings::matchAll($fileInfo->getContents(), self::CLASS_STATIC_CALL_REGEX);
            if ($matches === []) {
                continue;
            }

            foreach ($matches as $foundMatch) {
                $className = $foundMatch[self::CLASS_KEY_PART];
                $methodName = $foundMatch[self::METHOD_KEY_PART];

                if (method_exists($className, $methodName)) {
                    continue;
                }

                $errorMessage = sprintf('Method "%s::%s()" was not found.', $className, $methodName);
                $latteErrors[] = new LatteError($errorMessage, $fileInfo);
            }
        }

        return $latteErrors;
    }
}
