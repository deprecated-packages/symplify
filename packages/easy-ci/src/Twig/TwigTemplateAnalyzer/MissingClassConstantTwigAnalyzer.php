<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Twig\TwigTemplateAnalyzer;

use Nette\Utils\Strings;
use Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
use Symplify\EasyCI\Twig\Contract\TwigTemplateAnalyzerInterface;
use Symplify\EasyCI\ValueObject\FileError;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\EasyCI\Tests\Twig\TwigTemplateAnalyzer\MissingClassConstantTwigAnalyzer\MissingClassConstantTwigAnalyzerTest
 */
final class MissingClassConstantTwigAnalyzer implements TwigTemplateAnalyzerInterface
{
    /**
     * @see https://regex101.com/r/1Mt4ke/1
     * @var string
     */
    private const CLASS_CONSTANT_REGEX = '#constant\(\'(?<' . self::CLASS_CONSTANT_NAME_PART . '>[A-Z][\w\\\\]+::[A-Z0-9_]+)\'\)#m';

    /**
     * @var string
     */
    private const CLASS_CONSTANT_NAME_PART = 'class_constant_name';

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return FileErrorInterface[]
     */
    public function analyze(array $fileInfos): array
    {
        $templateErrors = [];

        foreach ($fileInfos as $fileInfo) {
            $matches = Strings::matchAll($fileInfo->getContents(), self::CLASS_CONSTANT_REGEX);
            if ($matches === []) {
                continue;
            }

            foreach ($matches as $match) {
                $classConstantName = (string) $match[self::CLASS_CONSTANT_NAME_PART];

                $classConstantName = str_replace('\\\\', '\\', $classConstantName);
                if (defined($classConstantName)) {
                    continue;
                }

                $errorMessage = sprintf('Class constant "%s" not found', $classConstantName);
                $templateErrors[] = new FileError($errorMessage, $fileInfo);
            }
        }

        return $templateErrors;
    }
}
