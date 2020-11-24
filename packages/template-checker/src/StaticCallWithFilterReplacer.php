<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\TemplateChecker\Tests\StaticCallWithFilterReplacer\StaticCallWithFilterReplacerTest
 */
final class StaticCallWithFilterReplacer
{
    /**
     * @var string
     * @see https://regex101.com/r/7lImz9/3
     * @see https://stackoverflow.com/a/35271017/1348344 for bracket matching on arguments
     */
    private const STATIC_CALL_REGEX = '#\b(?<class>[A-Z][\w\\\\]+)::(?<method>[\w]+)\((?<arguments>(?:[^)(]+|\((?:[^)(]+|\([^)(]*\))*\))*)\)#m';

    public function processFileInfo(SmartFileInfo $fileInfo): string
    {
        $contents = $fileInfo->getContents();

        return Strings::replace($contents, self::STATIC_CALL_REGEX, static function (array $match) {
            if (in_array($match['class'], [Strings::class, DateTime::class], true)) {
                // no change
                return $match[0];
            }

            return $match['method'] . '(' . $match['arguments'] . ')';
        });
    }
}
