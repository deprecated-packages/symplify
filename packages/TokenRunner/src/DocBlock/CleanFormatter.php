<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\Types\Array_;

/**
 * Keeps mixed[] as mixed[], not array
 */
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

    public function format(Tag $tag): string
    {
        $tagTypeAndDescription = ltrim((string) $tag, '\\');

        if (($tag instanceof Return_ || $tag instanceof Param)  && $tag->getType() instanceof Array_) {
            $tagTypeAndDescription = $this->resolveAndFixArrayTypeIfNeeded($tag, $tagTypeAndDescription);
        }

        return trim('@' . $tag->getName() . ' ' . $tagTypeAndDescription);
    }

    /**
     * @param Param|Return_ $tag
     */
    private function resolveAndFixArrayTypeIfNeeded(Tag $tag, string $tagTypeAndDescription): string
    {
        $original = 'array';

        if ($tag instanceof Param) {
            $original = ArrayResolver::resolveArrayType(
                $this->originalContent,
                $tag->getType(),
                'param',
                $tag->getVariableName()
            );
        }

        if ($tag instanceof Return_) {
            $original = ArrayResolver::resolveArrayType($this->originalContent, $tag->getType(), 'return');
        }

        // possible mixed[] override
        if ($original !== 'array' && ) {
            $tagTypeAndDescription = substr_replace($tagTypeAndDescription, 'mixed[]', 0, strlen('array'));
        }

        return $tagTypeAndDescription;
    }
}
