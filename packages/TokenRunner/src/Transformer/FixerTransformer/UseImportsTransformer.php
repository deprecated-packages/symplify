<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Transformer\FixerTransformer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\NamespaceFinder;
use Symplify\TokenRunner\Naming\Name\Name;
use Symplify\TokenRunner\Naming\UseImport\UseImportsFactory;

final class UseImportsTransformer
{
    /**
     * @param Name[] $names
     */
    public static function addNamesAsUseImportsToTokens(array $names, Tokens $tokens): void
    {
        $names = self::namesUnique($names);

        // find namespace start
        $namespacePosition = NamespaceFinder::findInTokens($tokens);
        $namespaceSemicolonPosition = $tokens->getNextTokenOfKind($namespacePosition, [';']);

        $useImports = (new UseImportsFactory())->createForTokens($tokens);
        // turn names into use import tokens
        $useTokens = [];
        foreach ($names as $name) {
            // skip already existing use imports
            foreach ($useImports as $useImport) {
                if ($name->getName() === $useImport->getFullName()) {
                    continue 2;
                }
            }

            $useTokens = array_merge($useTokens, self::buildUseTokensFromName($name));
        }

        $tokens->insertAt($namespaceSemicolonPosition + 2, $useTokens);
    }

    /**
     * @return Token[]
     */
    private static function buildUseTokensFromName(Name $name): array
    {
        $tokens = [
            new Token([T_USE, 'use']),
            new Token([T_WHITESPACE, ' '])
        ];

        if ($name->getRelatedUseImport()) {
            $tokens = self::addRelateUseImport($name, $tokens);
        }

        $tokens = array_merge($tokens, $name->getNameTokens());

        if ($name->getAlias()) {
            $tokens = self::addAlias($name, $tokens);
        }

        $tokens[] = new Token(';');
        $tokens[] = new Token([T_WHITESPACE, PHP_EOL]);

        return $tokens;
    }

    /**
     * @param Token[] $tokens
     * @return Token[]
     */
    private static function addRelateUseImport(Name $name, array $tokens): array
    {
        foreach ($name->getRelatedUseImport()->getNameParts() as $useDeclarationPart) {
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
    private static function addAlias(Name $name, array $tokens): array
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
    private static function namesUnique(array $names): array
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
}
