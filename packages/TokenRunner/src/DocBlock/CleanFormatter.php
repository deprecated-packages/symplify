<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;

final class CleanFormatter implements Formatter
{
    public function format(Tag $tag): string
    {
        return trim('@' . $tag->getName() . ' ' . ltrim((string) $tag, '\\'));
    }
}
