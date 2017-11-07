<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Import;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use SplFileInfo;
use Symplify\CodingStandard\FixerTokenWrapper\Naming\ClassFqnResolver;

/**
 * Possible cases.
 *
 * - 1. string that start with pre slash \SomeThing
 */
final class ImportNamespacedNameFixer implements FixerInterface
{
    /**
     * @var int
     */
    private $namespacePosition;

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_STRING);
    }

    public function isRisky(): bool
    {
        // first version is unable to deal with duplicated names
        return true;
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
//        $tokensAnalyzer = new TokensAnalyzer($tokens);
//        $uses = array_reverse($tokensAnalyzer->getImportUseIndexes());

        for ($index = $tokens->getSize() - 1; $index > 0; --$index) {
            $token = $tokens[$index];

            // case 1.
            if (! $this->isImportableName($tokens, $token, $index)) {
                continue;
            }

            $nameData = ClassFqnResolver::resolveDataFromEnd($tokens, $index);

            // replace with last name part
            $tokens->overrideRange(
                $nameData['start'],
                $nameData['end'],
                [
                    new Token([T_WHITESPACE, ' ']),
                    new Token([T_STRING, $nameData['lastPart']]),
                ]
            );

            // add use statement
            $this->addIntoUseStatements($tokens, $nameData['nameTokens']);

            // increase index to skip all used tokens
            $index = $nameData['start'];
        }
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getPriority(): int
    {
        // @todo: run before namespace sorting sorting
        return 0;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function getNamespacePosition(Tokens $tokens): int
    {
        if ($this->namespacePosition) {
            return $this->namespacePosition;
        }

        $namespace = $tokens->findGivenKind(T_NAMESPACE);
        reset($namespace);

        return $this->namespacePosition = key($namespace);
    }

    /**
     * @param Token[] $nameTokens
     */
    private function addIntoUseStatements(Tokens $tokens, array $nameTokens): void
    {
        $namespacePosition = $this->getNamespacePosition($tokens);
        $namespaceSemicolonPosition = $tokens->getNextTokenOfKind($namespacePosition, [';']);

        $tokens->insertAt(
            $namespaceSemicolonPosition + 2,
            $this->createUseStatementTokens($nameTokens)
        );
    }

    private function isImportableName(Tokens $tokens, Token $token, int $index): bool
    {
        if (! $token->isGivenKind(T_STRING)) {
            return false;
        }

        // already part of another namespaced name
        if ($tokens[$index + 1]->isGivenKind(T_NS_SEPARATOR)) {
            return false;
        }

        if ($tokens[$index - 2]->isGivenKind(T_NAMESPACE)) {
            // namespace: namespace "SomeName"
            return false;
        }

        if ($tokens[$index - 2]->isGivenKind(T_USE)) {
            // use statement: use "SomeName"
            return false;
        }

        return true;
    }

    /**
     * @param Token[] $nameTokens
     * @return Token[]
     */
    private function createUseStatementTokens(array $nameTokens): array
    {
        $tokens = [];

        $tokens[] = new Token([T_USE, 'use']);
        $tokens[] = new Token([T_WHITESPACE, ' ']);
        $tokens = array_merge($tokens, $nameTokens);
        $tokens[] = new Token(';');
        $tokens[] = new Token([T_WHITESPACE, PHP_EOL]);

        return $tokens;
    }
}
