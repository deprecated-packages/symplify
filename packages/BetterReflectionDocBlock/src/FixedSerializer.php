<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symplify\BetterReflectionDocBlock\Renderer\OriginalSpacingCompleter;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

/**
 * Includes empty indent fix
 *
 * @see https://github.com/phpDocumentor/ReflectionDocBlock/pull/138
 */
final class FixedSerializer extends Serializer
{
    /**
     * @var string
     */
    private $originalContent;

    public function setOriginalContent(string $originalContent): void
    {
        $this->originalContent = $originalContent;
    }

    public function getDocComment(DocBlock $docBlock): string
    {
        $indent = str_repeat($this->indentString, $this->indent);
        $firstIndent = $this->isFirstLineIndented ? $indent : '';
        // 3 === strlen(' * ')
        $wrapLength = $this->lineLength ? $this->lineLength - strlen($indent) - 3 : null;

        $text = $this->prepareText($docBlock, $indent, $wrapLength);

        // opening tag
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

            if ($this->originalContent) {
                $comment = (new OriginalSpacingCompleter())->completeTagSpaces($comment, $this->originalContent);
            }
        }

        // closing tag
        return $comment . $indent . ' */';
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
