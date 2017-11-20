<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Naming\Name;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Naming\UseImport\UseImportsFactory;

final class NameFactory
{
    public static function createFromTokensAndEnd(Tokens $tokens, int $end): Name
    {
        $previousTokenPointer = $end;

        [$nameTokens, $previousTokenPointer] = self::collectNameTokens($tokens, $previousTokenPointer);

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
        $name = self::resolveForName($tokens, $name);

        return new Name($previousTokenPointer, $end, $name, $nameTokens, $tokens);
    }

    /**
     * Inverse direction to @see createFromTokensAndEnd()
     */
    public static function createFromTokensAndStart(Tokens $tokens, int $start): Name
    {
        $nameTokens = [];

        $nextTokenPointer = $start;

        $prependNamespace = self::shouldPrependNamespace($tokens, $nextTokenPointer);

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
        $name = self::resolveForName($tokens, $name, $prependNamespace);

        return new Name($nextTokenPointer, $start, $name, $nameTokens, $tokens);
    }

    public static function resolveForName(Tokens $tokens, string $className, ?bool $prependNamespace = false): string
    {
        // probably not a class name, skip
        if (ctype_lower($className[0])) {
            return $className;
        }

        $useImports = (new UseImportsFactory())->createForTokens($tokens);

        foreach ($useImports as $useImport) {
            if ($className === $useImport->getShortName()) {
                return $useImport->getFullName();
            }
        }

        if ($prependNamespace) {
            $namespaceTokens = $tokens->findGivenKind([T_NAMESPACE], 0);
            if (count($namespaceTokens)) {
                $namespaceToken = array_pop($namespaceTokens);
                reset($namespaceToken);
                $namespacePosition = key($namespaceToken);

                [$nameTokens, $previousTokenPointer] = self::collectNameTokens($tokens, $namespacePosition + 2);

                $namespaceName = '';
                /** @var Token[] $nameTokens */
                foreach ($nameTokens as $nameToken) {
                    $namespaceName .= $nameToken->getContent();
                }

                $className = $namespaceName . '\\' . $className;
            }
        }

        return $className;
    }

    /**
     * @return mixed[][]
     */
    private static function collectNameTokens(Tokens $tokens, int $position): array
    {
        $nameTokens = [];

        while ($tokens[$position]->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
            $nameTokens[] = $tokens[$position];
            --$position;
        }

        return [$nameTokens, $position];
    }

    private static function shouldPrependNamespace(Tokens $tokens, int $position): bool
    {
        if ($tokens[$position - 1]->isGivenKind(T_NS_SEPARATOR)) {
            return false;
        }

        if ($tokens[$position]->isGivenKind(T_NS_SEPARATOR)) {
            return false;
        }

        return true;
    }

    /**
     * @todo merge with one above in private method and direction switcher
     */
}
