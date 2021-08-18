<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Twig\TwigTemplateAnalyzer;

use Nette\Utils\Strings;
use Symplify\EasyCI\Twig\Contract\TwigTemplateAnalyzerInterface;
use Symplify\EasyCI\ValueObject\TemplateError;
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
     * @return TemplateError[]
     */
    public function analyze(array $fileInfos): array
    {
        $templateErrors = [];

        foreach ($fileInfos as $fileInfo) {
            $matches = Strings::matchAll($fileInfo->getContents(), self::CLASS_CONSTANT_REGEX);
            if ($matches === []) {
                continue;
            }

            foreach ($matches as $foundMatch) {
                $classConstantName = (string) $foundMatch[self::CLASS_CONSTANT_NAME_PART];

                $classConstantName = str_replace('\\\\', '\\', $classConstantName);
                if (defined($classConstantName)) {
                    continue;
                }

                $errorMessage = sprintf('Class constant "%s" not found', $classConstantName);
                $templateErrors[] = new TemplateError($errorMessage, $fileInfo);
            }
        }

        return $templateErrors;
    }
}
