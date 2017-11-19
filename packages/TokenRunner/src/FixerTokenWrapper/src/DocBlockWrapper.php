<?php declare(strict_types=1);

namespace Symplify\CodingStandard\FixerTokenWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

final class DocBlockWrapper
{
    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    /**
     * @var int
     */
    private $docBlockPosition;

    private function __construct(Tokens $tokens, int $docBlockPosition, DocBlock $docBlock)
    {
        $this->tokens = $tokens;
        $this->docBlockPosition = $docBlockPosition;
        $this->docBlock = $docBlock;
    }

    public static function createFromTokensPositionAndDocBlock(
        Tokens $tokens,
        int $docBlockPosition,
        DocBlock $docBlock
    ): self {
        return new self($tokens, $docBlockPosition, $docBlock);
    }

    public function isSingleLine(): bool
    {
        return count($this->docBlock->getLines()) === 1;
    }

    public function getReturnType(): ?string
    {
        $returnAnnotations = $this->docBlock->getAnnotationsOfType('return');
        if (! $returnAnnotations) {
            return null;
        }

        $content = $this->resolveAnnotationContent($returnAnnotations[0], 'return');

        return ltrim($content, '\\');
    }

    public function getReturnTypeDescription(): ?string
    {
        $returnAnnotations = $this->docBlock->getAnnotationsOfType('return');
        if (! $returnAnnotations) {
            return null;
        }

        $returnAnnotation = $returnAnnotations[0];

        $annotationParts = explode(' ', $this->resolveAnnotationContent($returnAnnotation, 'return'));
        if (count($annotationParts) < 2) {
            return null;
        }

        [, $description] = $annotationParts;

        return $description;
    }

    public function getArgumentType(string $name): ?string
    {
        $paramAnnotations = $this->docBlock->getAnnotationsOfType('param');
        if (! $paramAnnotations) {
            return null;
        }

        foreach ($paramAnnotations as $paramAnnotation) {
            if (Strings::contains($paramAnnotation->getContent(), '$' . $name)) {
                $types = $this->resolveAnnotationContent($paramAnnotation, 'param');
                $typeParts = explode('$' . $name, $types);

                if (count($typeParts) < 2) {
                    return null;
                }

                [$type, ] = $typeParts;

                return trim($type, ' \\');
            }
        }

        return null;
    }

    public function getArgumentTypeDescription(string $name): ?string
    {
        $paramAnnotations = $this->docBlock->getAnnotationsOfType('param');
        if (! $paramAnnotations) {
            return null;
        }

        foreach ($paramAnnotations as $paramAnnotation) {
            if (Strings::contains($paramAnnotation->getContent(), '$' . $name)) {
                $annotationParts = explode('$' . $name, $this->resolveAnnotationContent($paramAnnotation, 'param'));

                if (count($annotationParts) < 2) {
                    return null;
                }

                return trim($annotationParts[1]);
            }
        }

        return null;
    }

    public function removeReturnType(): void
    {
        $returnAnnotations = $this->docBlock->getAnnotationsOfType('return');
        foreach ($returnAnnotations as $returnAnnotation) {
            $returnAnnotation->remove();
        }

        $this->tokens[$this->docBlockPosition] = new Token([T_DOC_COMMENT, $this->docBlock->getContent()]);
    }

    public function removeParamType(string $name): void
    {
        $paramAnnotations = $this->docBlock->getAnnotationsOfType('param');
        foreach ($paramAnnotations as $paramAnnotation) {
            if (Strings::contains($paramAnnotation->getContent(), '$' . $name)) {
                $paramAnnotation->remove();

                break;
            }
        }

        $this->tokens[$this->docBlockPosition] = new Token([T_DOC_COMMENT, $this->docBlock->getContent()]);
    }

    public function changeToMultiLine(): void
    {
        $indent = $this->whitespacesFixerConfig->getIndent();
        $lineEnding = $this->whitespacesFixerConfig->getLineEnding();
        $newLineWithIndent = $lineEnding . $indent;

        $newDocBlock = str_replace(
            [' @', '/** ', ' */'],
            [
                $newLineWithIndent . ' * @',
                '/**',
                $newLineWithIndent . ' */',
            ],
            $this->docBlock->getContent()
        );

        $this->tokens[$this->docBlockPosition] = new Token([T_DOC_COMMENT, $newDocBlock]);
    }

    public function setWhitespacesFixerConfig(WhitespacesFixerConfig $whitespacesFixerConfig): void
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    private function resolveAnnotationContent(Annotation $annotation, string $name): string
    {
        $content = $annotation->getContent();

        if ($content === '') {
            return $content;
        }

        [, $content] = explode('@' . $name, $content);

        $content = ltrim($content, ' *');
        $content = trim($content);

        return $content;
    }
}
