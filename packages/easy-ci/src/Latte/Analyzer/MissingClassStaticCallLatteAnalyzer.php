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
     * @see https://regex101.com/r/Wrfff2/8
     * @var string
     */
    private const CLASS_STATIC_CALL_REGEX = '#\b(?<class>[A-Z][\w\\\\]+)::(?<method>\w+)\(#m';

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return LatteError[]
     */
    public function analyze(array $fileInfos): array
    {
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
                    'Method "%s::%s()" was not found. "%s"',
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
