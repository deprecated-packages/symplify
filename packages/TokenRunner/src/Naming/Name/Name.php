<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Naming\Name;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class Name
{
    /**
     * @var int|null
     */
    private $start;

    /**
     * @var int|null
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
     * @var NamespaceUseAnalysis|null
     */
    private $relatedNamespaceUseAnalysis;

    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @param Token[] $nameTokens
     */
    public function __construct(?int $start, ?int $end, string $name, array $nameTokens, Tokens $tokens)
    {
        $this->start = $start;
        $this->end = $end;
        $this->name = $name;
        // to be sure indexing is from 0
        $this->nameTokens = array_values($nameTokens);
        $this->lastName = $this->nameTokens[count($this->nameTokens) - 1]->getContent();
        $this->tokens = $tokens;

        $namespaceUseAnalyses = (new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($this->tokens);
        foreach ($namespaceUseAnalyses as $namespaceUseAnalysis) {
            if (Strings::startsWith($this->name, $namespaceUseAnalysis->getShortName())) {
                $this->relatedNamespaceUseAnalysis = $namespaceUseAnalysis;
                $this->name = $this->composePartialNamespaceAndName($namespaceUseAnalysis->getFullName(), $this->name);
            }
        }
    }

    public function getStart(): ?int
    {
        return $this->start;
    }

    public function getEnd(): ?int
    {
        return $this->end;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Token[]
     */
    public function getNameTokens(): array
    {
        return $this->nameTokens;
    }

    public function getLastName(): string
    {
        if ($this->alias) {
            return $this->alias;
        }

        return $this->lastName;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function addAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    public function getFirstName(): string
    {
        return $this->nameTokens[0]->getContent();
    }

    public function getLastNameToken(): Token
    {
        return new Token([T_STRING, $this->getLastName()]);
    }

    public function isSingleName(): bool
    {
        return count($this->nameTokens) === 1;
    }

    public function getRelatedNamespaceUseAnalysis(): ?NamespaceUseAnalysis
    {
        return $this->relatedNamespaceUseAnalysis;
    }

    private function composePartialNamespaceAndName(string $namespace, string $name): string
    {
        if ($namespace === $name) {
            return $name;
        }

        $namespaceParts = explode('\\', $namespace);
        $nameParts = explode('\\', $name);

        $nameParts = array_merge($namespaceParts, $nameParts);
        $nameParts = array_unique($nameParts);

        return implode('\\', $nameParts);
    }
}
