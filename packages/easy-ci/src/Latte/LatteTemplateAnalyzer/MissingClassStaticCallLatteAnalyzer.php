<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte\LatteTemplateAnalyzer;

use Nette\Utils\Strings;
use Symplify\EasyCI\Latte\Contract\LatteTemplateAnalyzerInterface;
use Symplify\EasyCI\ValueObject\TemplateError;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\Latte\LatteTemplateAnalyzer\MissingClassStaticCallLatteAnalyzer\MissingClassStaticCallLatteAnalyzerTest
 */
final class MissingClassStaticCallLatteAnalyzer implements LatteTemplateAnalyzerInterface
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
     * @return TemplateError[]
     */
    public function analyze(array $fileInfos): array
    {
        $templateErrors = [];

        foreach ($fileInfos as $fileInfo) {
            $matches = Strings::matchAll($fileInfo->getContents(), self::CLASS_STATIC_CALL_REGEX);
            if ($matches === []) {
                continue;
            }

            foreach ($matches as $foundMatch) {
                $className = (string) $foundMatch[self::CLASS_KEY_PART];
                $methodName = (string) $foundMatch[self::METHOD_KEY_PART];

                if (method_exists($className, $methodName)) {
                    continue;
                }

                $errorMessage = sprintf('Method "%s::%s()" not found', $className, $methodName);
                $templateErrors[] = new TemplateError($errorMessage, $fileInfo);
            }
        }

        return $templateErrors;
    }
}
