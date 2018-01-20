<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\Renderer;

use Nette\Utils\Strings;

/**
 * When original content had an empty line before @return tag,
 * this service will return them.
 */
final class OriginalSpacingCompleter
{
    /**
     * @var string
     */
    private const TAG_WITH_SPACE_PATTERN = '#\s\*[\s]+\*\s@(?<tag>[a-z]+)#';

    public function completeTagSpaces(string $newContent, string $originalContent): string
    {
        $result = Strings::matchAll($originalContent, self::TAG_WITH_SPACE_PATTERN);
        if (! count($result)) {
            return $newContent;
        }

        foreach ($result as $match) {
            $tag = $match['tag'];
            $newContent = Strings::replace($newContent,'#(?<new_line>[\s]+\*\s)@' . $tag . '#', function (array $match) {
                return rtrim($match['new_line']) . $match[0];
            }, 1);
        }

        return $newContent;
    }
}
