<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Naming\Name;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Naming\UseImport\UseImportsFactory;

final class NameFactory
{
    public static function createFromTokensAndEnd(Tokens $tokens, int $end): Name
    {
        $nameTokens = [];

        $previousTokenPointer = $end;

        while ($tokens[$previousTokenPointer]->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
            $nameTokens[] = $tokens[$previousTokenPointer];
            --$previousTokenPointer;
        }

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
        $name = self::resolveForName($tokens, $name);

        return new Name($nextTokenPointer, $start, $name, $nameTokens, $tokens);
    }

    public static function resolveForName(Tokens $tokens, string $className): string
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

        return $className;
    }
}
