<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock;

use Nette\Utils\Strings;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Object_;
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
        if ($original === 'array' || $original === 'array[]') {
            return $tagTypeAndDescription;
        }

        if (Strings::contains($tagTypeAndDescription, 'array') && Strings::contains('mixed[]', $original)) {
            return substr_replace($tagTypeAndDescription, 'mixed[]', 0, strlen('array'));
        }

        return $tagTypeAndDescription;
    }

    private function addOriginalPreslashes(Tag $tag, string $tagTypeAndDescription): string
    {
        if ($tag instanceof TolerantReturn || $tag instanceof TolerantParam) {
            // FirstType
            if ($tag->getType() instanceof Object_) {
                // ReflectionDocBlock always adds types, so we need to check if original content had them
                $typeWithPreslash = (string) $tag->getType();
                if ($this->shouldAddPreslashToSingleType($tag, $typeWithPreslash)) {
                    return $tagTypeAndDescription;
                }

                return ltrim($tagTypeAndDescription, '\\');
            }

            // FirstType|SecondType
            if ($tag->getType() instanceof Compound) {
                $types = [];
                /** @var Compound $compoundTypes */
                $compoundTypes = $tag->getType();
                foreach ($compoundTypes as $type) {
                    $typeWithPreslash = (string) $type;
                    if ($this->shouldAddPreslashToSingleType($tag, $typeWithPreslash)) {
                        $types[] = $typeWithPreslash;
                    } else {
                        $types[] = ltrim($typeWithPreslash, '\\');
                    }
                }

                $types = implode('|', $types);

                [$oldTypes, $nameAndDescription] = explode(' ', (string) $tag);

                return trim($types . ' ' . $nameAndDescription);
            }
        }

        // fallback for other types
        if ($this->shouldAddPreslashToSingleType($tag, (string) $tag)) {
            return $tagTypeAndDescription;
        }

        return ltrim($tagTypeAndDescription, '\\');
    }

    /**
     * Matches:
     * - @name \<Type>
     * - @name \PreviousType|\<Type>
     * - @name PreviousType|\<Type> $name
     */
    private function shouldAddPreslashToSingleType(Tag $tag, string $singleType): bool
    {
        $exactRowPattern = sprintf(
            '#@%s[\s]+([a-zA-Z\\\\]+\|)*(?<pre_slash>\\\\)%s#',
            $tag->getName(),
            preg_quote(ltrim($singleType, '\\'), '#')
        );

        $match = Strings::match($this->originalContent, $exactRowPattern);

        return isset($match['pre_slash']);
    }
}
