<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\DocBlock;

use Nette\Utils\Strings;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;

final class ArrayResolver
{
    public static function resolveArrayType(
        string $originalContent,
        Array_ $arrayType,
        string $tagName,
        ?string $propertyName = null
    ): string {
        if ($arrayType->getValueType() instanceof Mixed_) {
            $matched = self::matchArrayOrMixedAnnotation($originalContent, $tagName, $propertyName);
            if ($matched) {
                return $matched['type'];
            }
        }

        // nested array
        if ($arrayType->getValueType() instanceof Array_) {
            return self::resolveArrayType($originalContent, $arrayType->getValueType(), $tagName, $propertyName);
        }

        if ($arrayType->getValueType() instanceof String_ ||
            $arrayType->getValueType() instanceof Callable_ ||
            $arrayType->getValueType() instanceof Null_ ||
            $arrayType->getValueType() instanceof Integer ||
            $arrayType->getValueType() instanceof Object_
        ) {
            return (string) $arrayType->getValueType() . '[]';
        }

        return 'array';
    }

    /**
     * Matches:
     *
     * - @{param} array $propertyName
     * - @{param} mixed[] $propertyName
     * - @{return} array
     * - @{return} mixed[]
     *
     * @return mixed[]|null
     */
    private static function matchArrayOrMixedAnnotation(
        string $originalContent,
        string $tagName,
        ?string $propertyName = null
    ): ?array {
        $mask = sprintf('@%s\s+(?<type>array\[\]|array|mixed(\[\])+)', $tagName);
        if ($propertyName) {
            $mask .= sprintf('\s+\$%s', $propertyName);
        }

        return Strings::match($originalContent, '#' . $mask . '#');
    }
}
