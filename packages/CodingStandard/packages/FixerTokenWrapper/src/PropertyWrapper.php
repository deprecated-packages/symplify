<?php declare(strict_types=1);

namespace Symplify\CodingStandard\FixerTokenWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Exception\UnexpectedTokenException;
use Symplify\CodingStandard\FixerTokenWrapper\Exception\MissingDocBlockException;
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
     * @var Token
     */
    private $visibilityToken;

    /**
     * @var int
     */
    private $visibilityPosition;

    /**
     * @var int|null
     */
    private $docBlockPosition;

    private function __construct(Tokens $tokens, int $index)
    {
        $this->ensureIsPropertyToken($tokens[$index]);


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
        $this->ensureHasDocBlock(__METHOD__);

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
        $propertyNameToken = $this->tokens[$this->getPropertyNamePosition()];

        return ltrim($propertyNameToken->getContent(), '$');
    }

    public function getType(): ?string
    {
        $this->ensureHasDocBlock(__METHOD__);

        $varAnnotations = $this->docBlock->getAnnotationsOfType('var');

        /** @var Annotation $varAnnotation */
        $varAnnotation = $varAnnotations[0];

        if (! isset ($varAnnotation->getTypes()[0])) {
            return null;
        }

        return $varAnnotation->getTypes()[0];
    }

    private function ensureHasDocBlock(string $calledMethod): void
    {
        if ($this->docBlock === null) {
            throw new MissingDocBlockException(sprintf(
                'Property %s does not have a docblock. So method "%s::%s()" cannot be used.',
                $this->getName(),
                self::class,
                $calledMethod
            ));
        }
    }

    public function changeName(string $newName): void
    {
        $newName = Strings::startsWith($newName, '$') ?: '$' . $newName;

        $this->tokens[$this->getPropertyNamePosition()] = new Token([T_VARIABLE, $newName]);
    }

    private function getPropertyNamePosition(): ?int
    {
        return $this->tokens->getNextMeaningfulToken($this->visibilityPosition);
    }

    private function ensureIsPropertyToken(Token $token): void
    {
        if ($token->isGivenKind(T_VARIABLE)) {
            return;
        }

        throw new UnexpectedTokenException(sprintf(
            '"%s" expected "%s" token in its constructor. "%s" token given.',
            self::class,
            implode(',', ['T_VARIABLE']),
            $token->getName()
        ));
    }
}
