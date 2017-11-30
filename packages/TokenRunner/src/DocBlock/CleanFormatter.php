<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;

final class CleanFormatter implements Formatter
{
    /**
     * @var string
     */
    private $originalContent;

    public function __construct(string $originalContent)
    {
        $this->originalContent = $originalContent;
    }

    // need original content

    public function format(Tag $tag): string
    {
        dump($this->originalContent);
        die;

        // keep mixed[] as mixed[], not array
        return trim('@' . $tag->getName() . ' ' . ltrim((string) $tag, '\\'));
    }
}
