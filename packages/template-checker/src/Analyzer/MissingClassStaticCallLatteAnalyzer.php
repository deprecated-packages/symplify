<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Analyzer;

use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;
use Webmozart\Assert\Assert;

/**
 * @see \Symplify\TemplateChecker\Tests\Analyzer\MissingClassStaticCallLatteAnalyzer\MissingClassStaticCallLatteAnalyzerTest
 */
final class MissingClassStaticCallLatteAnalyzer
{
    /**
     * @see https://regex101.com/r/Wrfff2/8
     * @var string
     */
    private const CLASS_STATIC_CALL_REGEX = '#\b(?<class>[A-Z][\w\\\\]+)::(?<method>\w+)\(#m';

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return string[]
     */
    public function analyze(array $fileInfos): array
    {
        Assert::allIsInstanceOf($fileInfos, SmartFileInfo::class);

        $errors = [];

        foreach ($fileInfos as $fileInfo) {
            $matches = Strings::matchAll($fileInfo->getContents(), self::CLASS_STATIC_CALL_REGEX);
            if ($matches === []) {
                continue;
            }

            foreach ($matches as $foundMatch) {
                if (method_exists($foundMatch['class'], $foundMatch['method'])) {
                    continue;
                }

                $error = sprintf(
                    'Method "%s::%s()" was not be found in "%s"',
                    $foundMatch['class'],
                    $foundMatch['method'],
                    $fileInfo->getRelativeFilePathFromCwd()
                );

                $errors[] = $error;
            }
        }

        return $errors;
    }
}
