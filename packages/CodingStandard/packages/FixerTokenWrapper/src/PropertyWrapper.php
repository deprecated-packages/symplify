<?php declare(strict_types=1);

namespace Symplify\CodingStandard\FixerTokenWrapper;

use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Tokenizer\DocBlockAnalyzer;
use Symplify\CodingStandard\Tokenizer\DocBlockFinder;
use Symplify\CodingStandard\Tokenizer\PropertyAnalyzer;

final class PropertyWrapper
{
    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var DocBlock|null
     */
    private $docBlock;

    /**
     * @var Token|null
     */
    private $visibilityToken;

    /**
     * @var int|null
     */
    private $visibilityPosition;

    /**
     * @var int|null
     */
    private $docBlockPosition;

    private function __construct(Tokens $tokens, int $index)
    {
        $this->tokens = $tokens;

        $this->docBlockPosition = DocBlockFinder::findPreviousPosition($tokens, $index);
        $docBlockToken = DocBlockFinder::findPrevious($tokens, $index);
        if ($docBlockToken) {
            $this->docBlock = new DocBlock($docBlockToken->getContent());
        }

        $this->visibilityPosition = PropertyAnalyzer::findVisibilityPosition($tokens, $index);
        $this->visibilityToken = $tokens[$this->visibilityPosition];
    }

    public static function createFromTokensAndPosition(Tokens $tokens, int $position): self
    {
        return new self($tokens, $position);
    }

    public function isInjectProperty(): bool
    {
        if ($this->visibilityToken === null) {
            return false;
        }

        if (! $this->visibilityToken->isGivenKind(T_PUBLIC)) {
            return false;
        }

        if ($this->docBlock === null) {
            return false;
        }

        if (! DocBlockAnalyzer::hasAnnotations($this->docBlock, ['inject', 'var'])) {
            return false;
        }

        return true;
    }

    public function removeAnnotation(string $annotationType): void
    {
        foreach ($this->docBlock->getAnnotationsOfType($annotationType) as $annotation) {
            $annotation->remove();
        }

        $this->tokens[$this->docBlockPosition] = new Token([T_DOC_COMMENT, $this->docBlock->getContent()]);
    }

    public function makePrivate(): void
    {
        $this->tokens[$this->visibilityPosition] = new Token([T_PRIVATE, 'private']);
    }

    public function getName(): string
    {
        $propertyNamePosition = $this->tokens->getNextMeaningfulToken($this->visibilityPosition);
        $propertyNameToken = $this->tokens[$propertyNamePosition];

        return ltrim($propertyNameToken->getContent(), '$');
    }

    public function getType(): string
    {
        $varAnnotations = $this->docBlock->getAnnotationsOfType('var');

        /** @var Annotation $varAnnotation */
        $varAnnotation = $varAnnotations[0];

        return $varAnnotation->getTypes()[0];
    }
}
