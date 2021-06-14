<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte\Analyzer;

use Nette\Utils\Strings;
use Symplify\EasyCI\Latte\Contract\LatteAnalyzerInterface;
use Symplify\EasyCI\Latte\ValueObject\LatteError;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\Latte\Analyzer\MissingClassConstantLatteAnalyzer\MissingClassConstantLatteAnalyzerTest
 */
final class MissingClassConstantLatteAnalyzer implements LatteAnalyzerInterface
{
    /**
     * @see https://regex101.com/r/Wrfff2/9
     * @var string
     */
    private const CLASS_CONSTANT_REGEX = '#\b(?<' . self::CLASS_CONSTANT_NAME_PART . '>[A-Z][\w\\\\]+::[A-Z0-9_]+)\b#m';

    /**
     * @var string
     */
    private const CLASS_CONSTANT_NAME_PART = 'class_constant_name';

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return LatteError[]
     */
    public function analyze(array $fileInfos): array
    {
        $latteErrors = [];

        foreach ($fileInfos as $fileInfo) {
            $matches = Strings::matchAll($fileInfo->getContents(), self::CLASS_CONSTANT_REGEX);
            if ($matches === []) {
                continue;
            }

            foreach ($matches as $foundMatch) {
                $classConstantName = $foundMatch[self::CLASS_CONSTANT_NAME_PART];
                if (defined($classConstantName)) {
                    continue;
                }

                $errorMessage = sprintf('Class constant "%s" not found', $classConstantName);
                $latteErrors[] = new LatteError($errorMessage, $fileInfo);
            }
        }

        return $latteErrors;
    }
}
