<?php declare(strict_types=1);

namespace Symplify\TokenRunner\FixerTokenWrapper\Naming;

use PhpCsFixer\Tokenizer\Token;

final class Name
{
    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $end;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Token[]
     */
    private $nameTokens = [];

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string|null
     */
    private $alias;

    /**
     * @var mixed[]|null
     */
    private $partialUseDeclaration;

    /**
     * @param Token[] $nameTokens
     */
    public function __construct(int $start, int $end, string $name, array $nameTokens)
    {
        $this->start = $start;
        $this->end = $end;
        $this->name = $name;
        $this->nameTokens = $nameTokens;
        $this->lastName = $this->nameTokens[count($this->nameTokens) - 1]->getContent();
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLastName(): string
    {
        if ($this->alias) {
            return $this->alias;
        }

        return $this->lastName;
    }

    public function addAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    public function getFirstName(): string
    {
        return $this->nameTokens[0]->getContent();
    }

    /**
     * @return Token[]
     */
    public function getUseNameTokens(): array
    {
        $tokens = [];

        $tokens[] = new Token([T_USE, 'use']);
        $tokens[] = new Token([T_WHITESPACE, ' ']);

        if ($this->partialUseDeclaration) {
            $startName = $this->nameTokens[0]->getContent();
            $useDeclarationParts = explode('\\', $this->partialUseDeclaration['fullName']);

            foreach ($useDeclarationParts as $useDeclarationPart) {
                if ($useDeclarationPart === $startName) {
                    break;
                }

                $tokens[] = new Token([T_STRING, $useDeclarationPart]);
                $tokens[] = new Token([T_NS_SEPARATOR, '\\']);
            }
        }

        $tokens = array_merge($tokens, $this->nameTokens);

        if ($this->alias) {
            $tokens[] = new Token([T_WHITESPACE, ' ']);
            $tokens[] = new Token([T_AS, 'as']);
            $tokens[] = new Token([T_WHITESPACE, ' ']);
            $tokens[] = new Token([T_STRING, $this->alias]);
        }

        $tokens[] = new Token(';');
        $tokens[] = new Token([T_WHITESPACE, PHP_EOL]);

        return $tokens;
    }

    public function getLastNameToken(): Token
    {
        return new Token([T_STRING, $this->getLastName()]);
    }

    /**
     * @param mixed[] $partialUseDeclaration
     */
    public function setPartialUseDeclaration(array $partialUseDeclaration): void
    {
        $this->partialUseDeclaration = $partialUseDeclaration;
    }

    public function isSingleName(): bool
    {
        return count($this->nameTokens) === 1;
    }
}
