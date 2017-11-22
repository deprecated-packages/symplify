<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\DocBlockFinder;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\PropertyAnalyzer;
use Symplify\TokenRunner\Guard\TokenTypeGuard;
use Symplify\TokenRunner\Naming\Name\NameFactory;

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
     * @var int
     */
    private $visibilityPosition;

    /**
     * @var int|null
     */
    private $docBlockPosition;

    /**
     * @var string|null
     */
    private $type;

    private function __construct(Tokens $tokens, int $index)
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$index], [T_VARIABLE], __METHOD__);

        $this->tokens = $tokens;

        $this->docBlockPosition = DocBlockFinder::findPreviousPosition($tokens, $index);
        $docBlockToken = DocBlockFinder::findPrevious($tokens, $index);
        if ($docBlockToken) {
            $this->docBlock = new DocBlock($docBlockToken->getContent());
        }

        $this->visibilityPosition = PropertyAnalyzer::findVisibilityPosition($tokens, $index);
    }

    public static function createFromTokensAndPosition(Tokens $tokens, int $position): self
    {
        return new self($tokens, $position);
    }

    public function getName(): string
    {
        $propertyNameToken = $this->tokens[$this->getPropertyNamePosition()];

        return ltrim($propertyNameToken->getContent(), '$');
    }

    public function getFqnType(): ?string
    {
        if ($this->getType() === null) {
            return null;
        }

        return NameFactory::resolveForName($this->tokens, $this->getType());
    }

    public function getType(): ?string
    {
        if ($this->type) {
            return $this->type;
        }

        if ($this->docBlock === null) {
            return null;
        }

        $varAnnotations = $this->docBlock->getAnnotationsOfType('var');

        /** @var Annotation $varAnnotation */
        $varAnnotation = $varAnnotations[0];

        if (! isset($varAnnotation->getTypes()[0])) {
            return null;
        }

        return implode('|', $varAnnotation->getTypes());
    }

    public function changeName(string $newName): void
    {
        $newName = Strings::startsWith($newName, '$') ?: '$' . $newName;

        $this->tokens[$this->getPropertyNamePosition()] = new Token([T_VARIABLE, $newName]);
    }

    public function isClassType(): bool
    {
        $type = $this->getType();
        if ($type === null) {
            return false;
        }

        if (in_array($type, ['string', 'int', 'bool', 'null', 'array'], true)) {
            return false;
        }

        if (Strings::contains($type, '[]')) {
            return false;
        }

        return true;
    }

    public function hasDocBlock(): bool
    {
        return $this->docBlock !== null;
    }

    public function getDocBlockWrapper(): ?DocBlockWrapper
    {
        if (! $this->hasDocBlock()) {
            return null;
        }

        return DocBlockWrapper::createFromTokensPositionAndDocBlock(
            $this->tokens,
            $this->docBlockPosition,
            $this->docBlock
        );
    }

    private function getPropertyNamePosition(): int
    {
        $nextVariableTokens = $this->tokens->findGivenKind(
            [T_VARIABLE],
            $this->visibilityPosition,
            $this->visibilityPosition + 5
        );

        $nextVariableToken = array_pop($nextVariableTokens);

        return key($nextVariableToken);
    }
}
