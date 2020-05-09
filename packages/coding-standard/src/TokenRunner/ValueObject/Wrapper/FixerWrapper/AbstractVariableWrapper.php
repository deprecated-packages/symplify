<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Naming\Name\NameFactory;
use Symplify\PackageBuilder\Php\TypeAnalyzer;

abstract class AbstractVariableWrapper
{
    /**
     * @var int
     */
    protected $index;

    /**
     * @var Tokens
     */
    protected $tokens;

    /**
     * @var TypeAnalyzer
     */
    private $typeAnalyzer;

    public function __construct(Tokens $tokens, int $index)
    {
        $this->tokens = $tokens;
        $this->index = $index;
        $this->typeAnalyzer = new TypeAnalyzer();
    }

    public function getName(): string
    {
        $nameToken = $this->tokens[$this->getNamePosition()];

        return ltrim($nameToken->getContent(), '$');
    }

    public function isClassType(): bool
    {
        $types = $this->getTypes();
        if ($types === []) {
            return false;
        }

        foreach ($types as $type) {
            if ($this->typeAnalyzer->isPhpReservedType($type)) {
                return false;
            }
        }

        foreach ($types as $type) {
            if ($this->typeAnalyzer->isIterableType($type)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        $previousTokenPosition = $this->tokens->getPrevMeaningfulToken($this->index);
        $previousToken = $this->tokens[$previousTokenPosition];

        if ($previousTokenPosition === null) {
            return [];
        }

        if ($previousToken->getContent() === '&') {
            $previousTokenPosition = $this->tokens->getPrevMeaningfulToken($previousTokenPosition);
            $previousToken = $this->tokens[$previousTokenPosition];
        }

        if (! $previousToken->isGivenKind([T_STRING, CT::T_ARRAY_TYPEHINT])) {
            return [];
        }

        // probably not a class type
        return [$previousToken->getContent()];
    }

    public function getFqnType(): ?string
    {
        $previousTokenPosition = $this->tokens->getPrevMeaningfulToken($this->index);
        if ($previousTokenPosition === null) {
            return null;
        }

        $name = (new NameFactory())->createFromTokensAndEnd($this->tokens, $previousTokenPosition);
        if ($name === null) {
            return null;
        }

        return $name->getName();
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    protected function changeNameWithTokenType(string $newName, int $tokenType): void
    {
        if ($tokenType === T_VARIABLE) {
            $newName = Strings::startsWith($newName, '$') ?: '$' . $newName;
        }

        $namePosition = $this->getNamePosition();
        if ($namePosition === null) {
            return;
        }

        $this->tokens[$namePosition] = new Token([$tokenType, $newName]);
    }

    abstract protected function getNamePosition(): ?int;
}
