<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock;

use Nette\Utils\Strings;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;
use phpDocumentor\Reflection\Types\Array_;
use Symplify\BetterReflectionDocBlock\Tag\TolerantParam;
use Symplify\BetterReflectionDocBlock\Tag\TolerantReturn;
use Symplify\TokenRunner\DocBlock\ArrayResolver;

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

        if (($tag instanceof TolerantReturn || $tag instanceof TolerantParam) && $tag->getType() instanceof Array_) {
            $tagTypeAndDescription = $this->resolveAndFixArrayTypeIfNeeded($tag, $tagTypeAndDescription);
        }

        $content = '@';
        $content .= $tag->getName() . ' ';
        if ($tagTypeAndDescription) {
            if ($this->shouldAddPreslash($tag)) {
                $content .= '\\';
            }
            $content .= $tagTypeAndDescription;
        }

        return trim($content);
    }

    /**
     * @param TolerantParam|TolerantReturn $tag
     */
    private function resolveAndFixArrayTypeIfNeeded(Tag $tag, string $tagTypeAndDescription): string
    {
        $original = 'array';

        if ($tag instanceof TolerantParam) {
            $original = ArrayResolver::resolveArrayType(
                $this->originalContent,
                $tag->getType(),
                'param',
                $tag->getVariableName()
            );
        }

        if ($tag instanceof TolerantReturn) {
            $original = ArrayResolver::resolveArrayType($this->originalContent, $tag->getType(), 'return');
        }

        // possible mixed[] override
        if ($original !== 'array' && $original !== 'array[]' && Strings::contains($tagTypeAndDescription, 'array')) {
            $tagTypeAndDescription = substr_replace($tagTypeAndDescription, 'mixed[]', 0, strlen('array'));
        }

        return $tagTypeAndDescription;
    }

    private function shouldAddPreslash(Tag $tag): bool
    {
        $typeWithoutPreslash = ltrim((string) $tag, '\\');

        $exactRowPattern = sprintf(
            '#@%s[\s]+(?<has_slash>\\\\)?%s#',
            $tag->getName(),
            preg_quote(trim($typeWithoutPreslash), '\\')
        );

        $match = Strings::match($this->originalContent, $exactRowPattern);

        return isset($match['has_slash']);
    }
}
