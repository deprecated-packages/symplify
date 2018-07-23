<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Naming\Name\NameFactory;

abstract class AbstractVariableWrapper
{
    /**
     * @var Tokens
     */
    protected $tokens;

    /**
     * @var int
     */
    protected $index;

    /**
     * @todo make use of TypeAnalyzer
     * @var string[]
     */
    private $simpleTypes = [
        'string',
        'int',
        'bool',
        'null',
        'array',
        'iterable',
        'integer',
        'boolean',
        'resource',
        'mixed',
    ];

    public function __construct(Tokens $tokens, int $index)
    {
        $this->tokens = $tokens;
        $this->index = $index;
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

        // just a simple type
        if (array_intersect($types, $this->simpleTypes)) {
            return false;
        }

        // @todo make use of TypeAnalyzer
        foreach ($types as $type) {
            if (Strings::contains($type, '[]')) {
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

        return $name->getName();
    }

    protected function changeNameWithTokenType(string $newName, int $tokenType): void
    {
        if ($tokenType === T_VARIABLE) {
            $newName = Strings::startsWith($newName, '$') ?: '$' . $newName;
        }

        $this->tokens[$this->getNamePosition()] = new Token([$tokenType, $newName]);
    }

    abstract protected function getNamePosition(): ?int;
}
