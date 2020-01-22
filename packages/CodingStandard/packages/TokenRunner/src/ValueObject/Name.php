<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\ValueObject;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
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
     * @var Tokens
     */
    private $tokens;

    public function __construct(?int $start, ?int $end, string $name, Tokens $tokens)
    {
        $this->start = $start;
        $this->end = $end;
        $this->name = $name;
        // to be sure indexing is from 0
        $this->tokens = $tokens;

        $namespaceUseAnalyses = (new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($this->tokens);
        foreach ($namespaceUseAnalyses as $namespaceUseAnalysis) {
            if (Strings::startsWith($this->name, $namespaceUseAnalysis->getShortName())) {
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
