<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

/**
 * Includes empty indent fix
 *
 * @See https://github.com/phpDocumentor/ReflectionDocBlock/pull/138
 */
final class FixedSerializer extends Serializer
{
    public function getDocComment(DocBlock $docBlock): string
    {
        $privatesCaller = new PrivatesCaller();

        $indent = str_repeat($this->indentString, $this->indent);
        $firstIndent = $this->isFirstLineIndented ? $indent : '';
        // 3 === strlen(' * ')
        $wrapLength = $this->lineLength ? $this->lineLength - strlen($indent) - 3 : null;

        $text = $privatesCaller->callPrivateMethod(
            $this,
            'removeTrailingSpaces',
            $indent,
            $privatesCaller->callPrivateMethod(
                $this,
                'addAsterisksForEachLine',
                $indent,
                $privatesCaller->callPrivateMethod($this, 'getSummaryAndDescriptionTextBlock', $docBlock, $wrapLength)
            )
        );

        $comment = "{$firstIndent}/**\n";
        if ($text) {
            $comment .= "{$indent} * {$text}\n";
            $comment .= "{$indent} *\n";
        }

        $comment = $privatesCaller->callPrivateMethod($this, 'addTagBlock', $docBlock, $wrapLength, $indent, $comment);
        $comment .= $indent . ' */';

        return $comment;
    }
}
