<?php declare(strict_types=1);

namespace Symplify\Statie\Templating;

final class ReadingTimeResolver
{
    /**
     * @var int
     */
    private const READ_WORDS_PER_MINUTE = 260;

    public function resolveFromContent(string $content): int
    {
        $wordCount = substr_count($content, ' ') + 1;

        return (int) ceil($wordCount / self::READ_WORDS_PER_MINUTE);
    }
}
