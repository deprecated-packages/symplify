<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte\Analyzer;

use Nette\Utils\Strings;
use Symplify\EasyCI\Latte\Contract\LatteAnalyzerInterface;
use Symplify\EasyCI\Latte\ValueObject\LatteError;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\Latte\Analyzer\SingleColonLatteAnalyzer\SingleColonLatteAnalyzerTest
 */
final class SingleColonLatteAnalyzer implements LatteAnalyzerInterface
{
    /**
     * @see https://regex101.com/r/Wrfff2/9
     * @var string
     */
    private const CLASS_CONSTANT_REGEX = '#\b(?<' . self::CLASS_CONSTANT_NAME_PART . '>[A-Z][\w\\\\]+:[A-Z_]+)\b#m';

    /**
     * @see https://regex101.com/r/Wrfff2/9
     * @var string
     */
    private const CALL_REGEX = '#\b(?<' . self::CLASS_CONSTANT_NAME_PART . '>[A-Z][\w\\\\]+:[A-Za-z_]+)\((.*?)?\)#m';

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

            foreach ($matches as $match) {
                $classConstantName = $match[self::CLASS_CONSTANT_NAME_PART];
                $errorMessage = sprintf('Single colon used in "%s" not found', $classConstantName);
                $latteErrors[] = new LatteError($errorMessage, $fileInfo);
            }
        }

        foreach ($fileInfos as $fileInfo) {
            $matches = Strings::matchAll($fileInfo->getContents(), self::CALL_REGEX);
            if ($matches === []) {
                continue;
            }

            foreach ($matches as $match) {
                $classConstantName = $match[self::CLASS_CONSTANT_NAME_PART];
                $errorMessage = sprintf('Single colon used in "%s" not found', $classConstantName);
                $latteErrors[] = new LatteError($errorMessage, $fileInfo);
            }
        }

        return $latteErrors;
    }
}
