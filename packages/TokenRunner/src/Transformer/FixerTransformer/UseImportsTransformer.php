<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Transformer\FixerTransformer;

use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\NamespaceFinder;
use Symplify\TokenRunner\Naming\Name\Name;

final class UseImportsTransformer
{
    /**
     * @var NamespaceUsesAnalyzer
     */
    private $namespaceUsesAnalyzer;

    public function __construct(NamespaceUsesAnalyzer $namespaceUsesAnalyzer)
    {
        $this->namespaceUsesAnalyzer = $namespaceUsesAnalyzer;
    }

    /**
     * @param Name[] $names
     */
    public function addNamesToTokens(array $names, Tokens $tokens): void
    {
        $namespaceUseAnalyses = $this->namespaceUsesAnalyzer->getDeclarationsFromTokens($tokens);

        $useTokens = [];
        $names = $this->namesUnique($names);
        foreach ($names as $name) {
            // skip already existing use imports
            foreach ($namespaceUseAnalyses as $namespaceUseAnalysis) {
                if ($name->getName() === $namespaceUseAnalysis->getFullName()) {
                    continue 2;
                }
            }

            // turn names into use import tokens
            $useTokens = array_merge($useTokens, $this->buildUseTokensFromName($name));
        }

        $tokens->insertAt($this->useStatementLocation($tokens), $useTokens);
    }

    /**
     * @return Token[]
     */
    private function buildUseTokensFromName(Name $name): array
    {
        $tokens = [
            new Token([T_USE, 'use']),
            new Token([T_WHITESPACE, ' ']),
        ];

        if ($name->getRelatedNamespaceUseAnalysis()) {
            $tokens = $this->addRelateUseImport($name, $tokens);
        }

        $tokens = array_merge($tokens, $name->getNameTokens());

        if ($name->getAlias()) {
            $tokens = $this->addAlias($name, $tokens);
        }

        $tokens[] = new Token(';');
        $tokens[] = new Token([T_WHITESPACE, PHP_EOL]);

        return $tokens;
    }

    /**
     * @param Token[] $tokens
     * @return Token[]
     */
    private function addRelateUseImport(Name $name, array $tokens): array
    {
        if ($name->getRelatedNamespaceUseAnalysis() === null) {
            return [];
        }

        $nameParts = explode('\\', $name->getRelatedNamespaceUseAnalysis()->getFullName());
        foreach ($nameParts as $useDeclarationPart) {
            if ($useDeclarationPart === $name->getFirstName()) {
                break;
            }

            $tokens[] = new Token([T_STRING, $useDeclarationPart]);
            $tokens[] = new Token([T_NS_SEPARATOR, '\\']);
        }

        return $tokens;
    }

    /**
     * @param Token[] $tokens
     * @return Token[]
     */
    private function addAlias(Name $name, array $tokens): array
    {
        $tokens[] = new Token([T_WHITESPACE, ' ']);
        $tokens[] = new Token([T_AS, 'as']);
        $tokens[] = new Token([T_WHITESPACE, ' ']);
        $tokens[] = new Token([T_STRING, $name->getAlias()]);

        return $tokens;
    }

    /**
     * @param Name[] $names
     * @return Name[]
     */
    private function namesUnique(array $names): array
    {
        $uniqueNames = [];
        foreach ($names as $name) {
            if (isset($uniqueNames[$name->getName()])) {
                continue;
            }

            $uniqueNames[$name->getName()] = $name;
        }

        return $uniqueNames;
    }

    private function useStatementLocation(Tokens $tokens): int
    {
        $namespacePosition = NamespaceFinder::findInTokens($tokens);
        if ($namespacePosition) {
            return $tokens->getNextTokenOfKind($namespacePosition, [';']) + 2;
        }

        $usePosition = $tokens->getNextTokenOfKind(0, [T_USE]);
        if ($usePosition) {
            return $usePosition;
        }

        $classPosition = $tokens->getNextTokenOfKind(0, [T_CLASS]);
        if ($classPosition) {
            return $classPosition - 3;
        }

        return 0;
    }
}
