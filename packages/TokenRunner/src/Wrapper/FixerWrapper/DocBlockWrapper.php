<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use phpDocumentor\Reflection\DocBlock as PhpDocumentorDocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

final class DocBlockWrapper
{
    /**
     * @var Tokens|null
     */
    private $tokens;

    /**
     * @var DocBlock|null
     */
    private $docBlock;

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    /**
     * @var int|null
     */
    private $docBlockPosition;

    /**
     * @var PhpDocumentorDocBlock
     */
    private $phpDocumentorDocBlock;

    private function __construct(?Tokens $tokens, ?int $docBlockPosition, ?DocBlock $docBlock, ?Token $token = null)
    {
        $this->tokens = $tokens;
        $this->docBlockPosition = $docBlockPosition;
        $this->docBlock = $docBlock;

        if ($docBlock === null && $token !== null) {
            $this->docBlock = new DocBlock($token->getContent());
        }

        $docBlockFactory = DocBlockFactory::createInstance();
        $content = $token ? $token->getContent() : $docBlock->getContent();
        $this->phpDocumentorDocBlock = $docBlockFactory->create($content);
    }

    public static function createFromTokensPositionAndDocBlock(
        Tokens $tokens,
        int $docBlockPosition,
        DocBlock $docBlock
    ): self {
        TokenTypeGuard::ensureIsTokenType($tokens[$docBlockPosition], [T_COMMENT, T_DOC_COMMENT], __METHOD__);

        return new self($tokens, $docBlockPosition, $docBlock);
    }

    public static function createFromDocBlockToken(Token $docBlockToken): self
    {
        TokenTypeGuard::ensureIsTokenType($docBlockToken, [T_COMMENT, T_DOC_COMMENT], __METHOD__);

        return new self(null, null, null, $docBlockToken);
    }

    public function isSingleLine(): bool
    {
        return count($this->docBlock->getLines()) === 1;
    }

    public function getReturnType(): ?string
    {
        $returnTags = $this->phpDocumentorDocBlock->getTagsByName('return');
        if (! $returnTags) {
            return null;
        }

        $content = (string) $returnTags[0];

        return $this->clean($content);
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

    public function isArrayProperty(): bool
    {
        if (! $this->docBlock->getAnnotationsOfType('var')) {
            return false;
        }

        $varAnnotation = $this->docBlock->getAnnotationsOfType('var')[0];

        $content = trim($varAnnotation->getContent());
        $content = rtrim($content, ' */');

        [, $types] = explode('@var', $content);

        $types = explode('|', trim($types));

        foreach ($types as $type) {
            if (! self::isIterableType($type)) {
                return false;
            }
        }

        return true;
    }

    public function contains(string $content): bool
    {
        return Strings::contains($this->docBlock->getContent(), $content);
    }

    private function isIterableType(string $type): bool
    {
        if (Strings::endsWith($type, '[]')) {
            return true;
        }

        if ($type === 'array') {
            return true;
        }

        return false;
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

    private function clean(string $content): string
    {
        return ltrim(trim($content), '\\');
    }
}
