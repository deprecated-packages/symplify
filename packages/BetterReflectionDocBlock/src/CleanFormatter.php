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
        $tagTypeAndDescription = (string) $tag;

        // remove slashes added automatically by ReflectionDocBlock
        $tagTypeAndDescription = ltrim($tagTypeAndDescription, '\\');
        $tagTypeAndDescription = str_replace('|\\', '|', $tagTypeAndDescription);

        if (($tag instanceof TolerantReturn || $tag instanceof TolerantParam) && $tag->getType() instanceof Array_) {
            $tagTypeAndDescription = $this->resolveAndFixArrayTypeIfNeeded($tag, $tagTypeAndDescription);
        }

        $content = '@' . $tag->getName() . ' ';
        if ($tagTypeAndDescription) {
            $content .= $this->addOriginalPreslashes($tag, $tagTypeAndDescription);
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

    private function addOriginalPreslashes(Tag $tag, string $tagTypeAndDescription): string
    {
        if (! $this->shouldAddPreslash($tag)) {
            return $tagTypeAndDescription;
        }

        return '\\' . $tagTypeAndDescription;
    }

    private function shouldAddPreslash(Tag $tag): bool
    {
        $typeWithoutPreslash = trim(ltrim((string) $tag, '\\'));

        // escape possibly breaking chars
        $typeWithoutPreslashQuoted = preg_quote($typeWithoutPreslash, '#');

        // this allows tabs as indent spaced, ReflectionDocBlock changes all to spaces
        $typeWithoutPreslashWithSpaces = str_replace(' ', '[\s]*', $typeWithoutPreslashQuoted);

        $exactRowPattern = sprintf(
            '#@%s[\s]+(?<has_slash>\\\\)%s#',
            $tag->getName(),
            $typeWithoutPreslashWithSpaces
        );

        return (bool) Strings::match($this->originalContent, $exactRowPattern);
    }
}
