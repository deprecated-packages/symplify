<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\DocBlockFinder;
use Symplify\TokenRunner\Guard\TokenTypeGuard;
use Symplify\TokenRunner\Naming\Name\NameFactory;

final class PropertyWrapper extends AbstractVariableWrapper
{
    /**
     * @var DocBlockWrapper|null
     */
    private $docBlockWrapper;

    protected function __construct(Tokens $tokens, int $index)
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$index], [T_VARIABLE], __METHOD__);

        parent::__construct($tokens, $index);

        $docBlockPosition = DocBlockFinder::findPreviousPosition($tokens, $index);
        if ($docBlockPosition) {
            $this->docBlockWrapper = DocBlockWrapper::createFromTokensAndPosition($this->tokens, $docBlockPosition);
        }
    }

    public static function createFromTokensAndPosition(Tokens $tokens, int $position): self
    {
        return new self($tokens, $position);
    }

    public function getName(): string
    {
        $propertyNameToken = $this->tokens[$this->getNamePosition()];

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
        $varTag = $this->docBlockWrapper->getVarTag();
        if ($varTag === null) {
            return null;
        }

        $varTagType = (string) $varTag->getType();
        $varTagType = trim($varTagType);

        return ltrim($varTagType, '\\');
    }

    public function changeName(string $newName): void
    {
        $this->changeNameWithTokenType($newName, T_VARIABLE);
    }

    public function getDocBlockWrapper(): ?DocBlockWrapper
    {
        return $this->docBlockWrapper;
    }

    protected function getNamePosition(): int
    {
        $nextVariableTokens = $this->tokens->findGivenKind(
            [T_VARIABLE],
            $this->index,
            $this->index + 5
        );

        $nextVariableToken = array_pop($nextVariableTokens);

        return key($nextVariableToken);
    }
}
