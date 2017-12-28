<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock;

use PhpCsFixer\WhitespacesFixerConfig;
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
        WhitespacesFixerConfig $whitespacesFixerConfig,
        string $originalContent
    ): Serializer {
        if ($whitespacesFixerConfig->getIndent() === '    ') {
            $indent = 4;
            $indentString = ' ';
        } else {
            $indent = 1;
            $indentString = $whitespacesFixerConfig->getIndent();
        }

        return new FixedSerializer($indent, $indentString, false, null, new CleanFormatter($originalContent));
    }
}
