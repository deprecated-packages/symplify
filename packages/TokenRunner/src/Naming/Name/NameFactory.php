<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Naming\Name;

use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class NameFactory
{
    public function createFromTokensAndEnd(Tokens $tokens, int $end): Name
    {
        $previousTokenPointer = $end;

        [$nameTokens, $previousTokenPointer] = $this->collectNameTokens($tokens, $previousTokenPointer);

        /** @var Token[] $nameTokens */
        $nameTokens = array_reverse($nameTokens);
        if ($nameTokens[0]->isGivenKind(T_NS_SEPARATOR)) {
            unset($nameTokens[0]);
            // reset array keys
            $nameTokens = array_values($nameTokens);

            // move start pointer after "\"
            ++$previousTokenPointer;
        }

        if (! $tokens[$previousTokenPointer]->isGivenKind([T_STRING, T_NS_SEPARATOR])) {
            ++$previousTokenPointer;
        }

        $name = '';
        foreach ($nameTokens as $nameToken) {
            $name .= $nameToken->getContent();
        }

        // resolve fully qualified name - as argument?
        $name = $this->resolveForName($tokens, $name);

        /** @var int $previousTokenPointer */
        return new Name($previousTokenPointer, $end, $name, $nameTokens, $tokens);
    }

    public function createFromStringAndTokens(string $name, Tokens $tokens): Name
    {
        $nameTokens = [];
        $name = ltrim($name, '\\');

        foreach (explode('\\', $name) as $namePart) {
            $nameTokens[] = new Token([T_NS_SEPARATOR, '\\']);
            $nameTokens[] = new Token([T_STRING, $namePart]);
        }

        // remove first pre slash
        unset($nameTokens[0]);

        return new Name(null, null, $name, $nameTokens, $tokens);
    }

    /**
     * Inverse direction to @see createFromTokensAndEnd()
     */
    public function createFromTokensAndStart(Tokens $tokens, int $start): Name
    {
        $nameTokens = [];

        $nextTokenPointer = $start;

        $prependNamespace = $this->shouldPrependNamespace($tokens, $nextTokenPointer);

        while ($tokens[$nextTokenPointer]->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
            $nameTokens[] = $tokens[$nextTokenPointer];
            ++$nextTokenPointer;
        }

        /** @var Token[] $nameTokens */
        if ($nameTokens[0]->isGivenKind(T_NS_SEPARATOR)) {
            unset($nameTokens[0]);
            // reset array keys
            $nameTokens = array_values($nameTokens);

            // move start pointer after "\"
            --$nextTokenPointer;
        }

        if (! $tokens[$nextTokenPointer]->isGivenKind([T_STRING, T_NS_SEPARATOR])) {
            --$nextTokenPointer;
        }

        $name = '';
        foreach ($nameTokens as $nameToken) {
            $name .= $nameToken->getContent();
        }

        // resolve fully qualified name - as argument?
        $name = $this->resolveForName($tokens, $name, $prependNamespace);

        return new Name($nextTokenPointer, $start, $name, $nameTokens, $tokens);
    }

    public function resolveForName(Tokens $tokens, string $className, ?bool $prependNamespace = false): string
    {
        if ($className === '') {
            return '';
        }

        // probably not a class name, skip
        if (ctype_lower($className[0])) {
            return $className;
        }

        $namespaceUseAnalyses = (new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens);
        foreach ($namespaceUseAnalyses as $namespaceUseAnalysis) {
            if ($className === $namespaceUseAnalysis->getShortName()) {
                return $namespaceUseAnalysis->getFullName();
            }
        }

        if (! $prependNamespace) {
            return $className;
        }

        $namespaceTokens = $tokens->findGivenKind([T_NAMESPACE], 0);
        if (! count($namespaceTokens[T_NAMESPACE])) {
            return $className;
        }

        $namespaceToken = array_pop($namespaceTokens);
        reset($namespaceToken);
        $namespacePosition = (int) key($namespaceToken);

        $namespaceName = '';
        $position = $namespacePosition + 2;
        while ($tokens[$position]->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
            $namespaceName .= $tokens[$position]->getContent();
            ++$position;
        }

        return $namespaceName . '\\' . $className;
    }

    /**
     * @return mixed[][]|mixed[]
     */
    private function collectNameTokens(Tokens $tokens, int $position): array
    {
        $nameTokens = [];

        while ($tokens[$position]->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
            $nameTokens[] = $tokens[$position];
            --$position;
        }

        return [$nameTokens, $position];
    }

    private function shouldPrependNamespace(Tokens $tokens, int $position): bool
    {
        if ($tokens[$position - 1]->isGivenKind(T_NS_SEPARATOR)) {
            return false;
        }

        if ($tokens[$position]->isGivenKind(T_NS_SEPARATOR)) {
            return false;
        }

        return true;
    }
}
