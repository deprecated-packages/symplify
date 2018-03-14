<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock;

use phpDocumentor\Reflection\DocBlock\Serializer;

/**
 * This factory resolves over native 2 bugs:
 *
 * - that adds spaces after empty tag
 * - that adds "\" prefix to every type
 */
final class DocBlockSerializerFactory
{
    public static function createFromWhitespaceFixerConfigAndContent(
        string $originalContent,
        int $indentSize,
        string $indentCharacter
    ): Serializer {
        $cleanFormatter = new CleanFormatter($originalContent);
        $fixedSerializer = new FixedSerializer($indentSize, $indentCharacter, false, null, $cleanFormatter);
        $fixedSerializer->setOriginalContent($originalContent);

        return $fixedSerializer;
    }
}
