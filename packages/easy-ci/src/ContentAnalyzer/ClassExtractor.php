<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ContentAnalyzer;

use Nette\Neon\Encoder;
use Nette\Neon\Neon;
use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ClassExtractor
{
    /**
     * @var string
     * @see https://regex101.com/r/1VKOxi/8
     */
    private const CLASS_NAME_REGEX = '#(?<quote>["\']?)\b(?<' . self::CLASS_NAME_PART . '>[A-Z](\w+\\\\(\\\\)?)+(\w+))(?<next_char>\\\\|\\\\:|(?&quote))?(?!:)$#m';

    /**
     * @var string
     * @see https://regex101.com/r/1IpNtV/3
     */
    private const STATIC_CALL_CLASS_REGEX = '#(?<quote>["\']?)[\\\\]*(?<class_name>[A-Z][\w\\\\]+)::#';

    /**
     * @var string
     */
    private const NEXT_CHAR = 'next_char';

    /**
     * @var string
     */
    private const CLASS_NAME_PART = 'class_name';

    /**
     * @var string
     * @see https://regex101.com/r/PV3HcW/1
     */
    private const NEWLINE_THEN_SPACE_REGEX = '#\n[ \t]+#';

    /**
     * @see https://regex101.com/r/TKgsqu/1
     * @var string
     */
    private const APPLICATION_START_REGEX = '#^(?<application_line>application:.*?)$#m';

    /**
     * @return class-string[]
     */
    public function extractFromFileInfo(SmartFileInfo $fileInfo): array
    {
        $classNames = [];
        $fileContent = $this->getFileContent($fileInfo);

        $matches = Strings::matchAll($fileContent, self::CLASS_NAME_REGEX);

        $fileContentWithRootLines = Strings::replace($fileContent, self::NEWLINE_THEN_SPACE_REGEX);

        // possibly mask for routes
        $applicationLine = $this->resolveNetteApplicationLine($fileContentWithRootLines);

        foreach ($matches as $match) {
            if (isset($match[self::NEXT_CHAR]) && ($match[self::NEXT_CHAR] === '\\' || $match[self::NEXT_CHAR] === '\\:')) {
                // is Symfony autodiscovery → skip
                continue;
            }

            $classNamePart = $match[self::CLASS_NAME_PART];
            if ($applicationLine && Strings::contains($applicationLine, $classNamePart)) {
                continue;
            }

            $classNames[] = $this->extractClassName($fileInfo, $match);
        }

        $matches = Strings::matchAll($fileContent, self::STATIC_CALL_CLASS_REGEX);
        foreach ($matches as $match) {
            $classNames[] = $this->extractClassName($fileInfo, $match);
        }

        return $classNames;
    }

    private function getFileContent(SmartFileInfo $fileInfo): string
    {
        if ($fileInfo->getSuffix() === 'neon') {
            $neon = Neon::decode($fileInfo->getContents());

            // section with no classes that resemble classes
            unset($neon['mapping']);

            return Neon::encode($neon, Encoder::BLOCK);
        }

        return $fileInfo->getContents();
    }

    /**
     * @param array<string, class-string> $match
     * @return class-string
     */
    private function extractClassName(SmartFileInfo $fileInfo, array $match): string
    {
        if ($fileInfo->getSuffix() === 'twig' && $match['quote'] !== '') {
            return str_replace('\\\\', '\\', $match[self::CLASS_NAME_PART]);
        }

        return $match[self::CLASS_NAME_PART];
    }

    private function resolveNetteApplicationLine(string $fileContentWithRootLines): string
    {
        $isMaskForRoutes = Strings::startsWith($fileContentWithRootLines, 'application:');
        if (! $isMaskForRoutes) {
            return '';
        }

        $matches = Strings::match($fileContentWithRootLines, self::APPLICATION_START_REGEX);
        return $matches['application_line'] ?? '';
    }
}
