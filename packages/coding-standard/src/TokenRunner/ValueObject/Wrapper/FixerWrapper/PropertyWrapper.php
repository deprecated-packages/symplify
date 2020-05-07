<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\DocBlock\DocBlockManipulator;
use Symplify\CodingStandard\TokenRunner\Naming\Name\NameFactory;

final class PropertyWrapper extends AbstractVariableWrapper
{
    /**
     * @var NameFactory
     */
    private $nameFactory;

    /**
     * @var DocBlockManipulator
     */
    private $docBlockManipulator;

    public function __construct(
        Tokens $tokens,
        int $index,
        NameFactory $nameFactory,
        DocBlockManipulator $docBlockManipulator
    ) {
        parent::__construct($tokens, $index);

        $this->nameFactory = $nameFactory;
        $this->docBlockManipulator = $docBlockManipulator;
    }

    public function getFqnType(): ?string
    {
        if ($this->getTypes() === []) {
            return null;
        }

        $types = $this->getTypes();
        /** @var string $type */
        $type = array_pop($types);

        return $this->nameFactory->resolveForName($this->tokens, $type);
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        $varTagValueNodes = $this->docBlockManipulator->resolveVarTagsIfFound($this->tokens, $this->index);

        $types = [];
        foreach ($varTagValueNodes as $varTagValueNode) {
            $types[] = (string) $varTagValueNode->type;
        }

        return $types;
    }

    public function changeName(string $newName): void
    {
        $this->changeNameWithTokenType($newName, T_VARIABLE);
    }

    protected function getNamePosition(): int
    {
        $expressionEndPosition = $this->tokens->getNextTokenOfKind($this->index, [';']);

        $nextVariableTokens = $this->tokens->findGivenKind([T_VARIABLE], $this->index, $expressionEndPosition);
        $nextVariableToken = array_pop($nextVariableTokens);

        return (int) key($nextVariableToken);
    }
}
