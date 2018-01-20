<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

/**
 * Includes empty indent fix
 *
 * @see https://github.com/phpDocumentor/ReflectionDocBlock/pull/138
 */
final class FixedSerializer extends Serializer
{
    public function getDocComment(DocBlock $docBlock): string
    {
        $indent = str_repeat($this->indentString, $this->indent);
        $firstIndent = $this->isFirstLineIndented ? $indent : '';
        // 3 === strlen(' * ')
        $wrapLength = $this->lineLength ? $this->lineLength - strlen($indent) - 3 : null;

        $text = $this->prepareText($docBlock, $indent, $wrapLength);

        // opening
        $comment = $firstIndent . '/**' . PHP_EOL;

        // description
        if ($text) {
            $comment .= $indent . ' * ' . $text . PHP_EOL;
        }

        // content and tags separator
        if ($text && $docBlock->getTags()) {
            $comment .= $indent . ' *' . PHP_EOL;
        }

        // tags
        $privatesCaller = new PrivatesCaller();
        if ($docBlock->getTags()) {
            $comment = $privatesCaller->callPrivateMethod(
                $this,
                'addTagBlock',
                $docBlock,
                $wrapLength,
                $indent,
                $comment
            );
        }

        // closing
        $comment .= $indent . ' */';

        return $comment;
    }

    private function prepareText(DocBlock $docBlock, string $indent, ?int $wrapLength): string
    {
        $privatesCaller = new PrivatesCaller();

        $summaryAndDescription = $privatesCaller->callPrivateMethod(
            $this,
            'getSummaryAndDescriptionTextBlock',
            $docBlock,
            $wrapLength
        );

        $summaryAndDescription = $privatesCaller->callPrivateMethod(
            $this,
            'addAsterisksForEachLine',
            $indent,
            $summaryAndDescription
        );

        return $privatesCaller->callPrivateMethod($this, 'removeTrailingSpaces', $indent, $summaryAndDescription);
    }
}
