<?php

declare(strict_types=1);

namespace Symplify\ClassPresence\Regex;

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
    private const CLASS_NAME_REGEX = '#(?<quote>["\']?)\b(?<class_name>[A-Z](\w+\\\\(\\\\)?)+(\w+))(?<next_char>\\\\|\\\\:|(?&quote))?(?!:)$#m';

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
     * @return string[]
     */
    public function extractFromFileInfo(SmartFileInfo $fileInfo): array
    {
        $classNames = [];
        $fileContent = $this->getFileContent($fileInfo);

        $matches = Strings::matchAll($fileContent, self::CLASS_NAME_REGEX);
        foreach ($matches as $match) {
            if (isset($match[self::NEXT_CHAR]) && ($match[self::NEXT_CHAR] === '\\' || $match[self::NEXT_CHAR] === '\\:')) {
                // is Symfony autodiscovery → skip
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

    private function extractClassName(SmartFileInfo $fileInfo, array $match): string
    {
        if ($fileInfo->getSuffix() === 'twig' && $match['quote'] !== '') {
            return str_replace('\\\\', '\\', $match['class_name']);
        }

        return $match['class_name'];
    }
}
