<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Naming\Name\NameFactory;

final class PropertyWrapper extends AbstractVariableWrapper
{
    /**
     * @var DocBlockWrapper|null
     */
    private $docBlockWrapper;

    /**
     * @var NameFactory
     */
    private $nameFactory;

    public function __construct(Tokens $tokens, int $index, ?DocBlockWrapper $docBlockWrapper, NameFactory $nameFactory)
    {
        parent::__construct($tokens, $index);

        $this->docBlockWrapper = $docBlockWrapper;
        $this->nameFactory = $nameFactory;
    }

    public function getName(): string
    {
        $propertyNameToken = $this->tokens[$this->getNamePosition()];

        return ltrim($propertyNameToken->getContent(), '$');
    }

    public function getFqnType(): ?string
    {
        if ($this->getTypes() === []) {
            return null;
        }

        $types = $this->getTypes();
        $type = array_pop($types);

        return $this->nameFactory->resolveForName($this->tokens, $type);
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->docBlockWrapper ? $this->docBlockWrapper->getVarTypes() : [];
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
        $nextVariableTokens = $this->tokens->findGivenKind([T_VARIABLE], $this->index, $this->index + 5);

        $nextVariableToken = array_pop($nextVariableTokens);

        return (int) key($nextVariableToken);
    }
}
