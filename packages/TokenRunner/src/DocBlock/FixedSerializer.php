<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;

/**
 * Includes empty indend fix
 */
final class FixedSerializer extends Serializer
{
    public function getDocComment(DocBlock $docblock): string
    {
        $indent = str_repeat($this->indentString, $this->indent);
        $firstIndent = $this->isFirstLineIndented ? $indent : '';
        // 3 === strlen(' * ')
        $wrapLength = $this->lineLength ? $this->lineLength - strlen($indent) - 3 : null;

        $text = $this->callPrivateMethod($this, 'removeTrailingSpaces',
            $indent,
            $this->callPrivateMethod($this,'addAsterisksForEachLine',
                $indent,
                $this->callPrivateMethod($this, 'getSummaryAndDescriptionTextBlock', $docblock, $wrapLength)
            )
        );

        $comment = "{$firstIndent}/**\n";

        if ($text) {
            $comment .= "{$indent} * {$text}\n";
            $comment .= "{$indent} *\n";
        }
        $comment = $this->callPrivateMethod($this, 'addTagBlock', $docblock, $wrapLength, $indent, $comment);
        $comment .= $indent . ' */';

        return $comment;
    }

    /**
     * @param object $object
     * @return mixed
     */
    private function callPrivateMethod($object, string $methodName, ...$arguments)
    {
        $classReflection = new \ReflectionClass(get_class($object));

        $methodReflection = $classReflection->getMethod($methodName);
        $methodReflection->setAccessible(true);

        return $methodReflection->invoke($object, ...$arguments);
    }
}
