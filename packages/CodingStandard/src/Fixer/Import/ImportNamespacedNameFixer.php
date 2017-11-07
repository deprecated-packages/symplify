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

        for ($index = 0; $index < $tokens->getSize(); ++$index) {
            $token = $tokens[$index];

            // case 1.
            if ($token->isGivenKind([T_NS_SEPARATOR]) && ! $tokens[$index - 1]->isGivenKind(T_STRING)) {
                $nameData = ClassFqnResolver::resolveDataFromStart($tokens, $index);

                // todo: put into top
                $this->addIntoUseStatements($tokens, $nameData['name']);

                $tokens->overrideRange(
                    $nameData['start'],
                    $nameData['end'],
                    [
                        new Token([T_WHITESPACE, ' ']),
                        new Token([T_STRING, $nameData['lastPart']])
                    ]
                );

                $index = $nameData['end'];

                // replace with last name part

                // increase index to skip all used tokens
                // $i+
            }
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

    private function addIntoUseStatements(Tokens $tokens, string $useStatement): void
    {
        $namespacePosition = $this->getNamespacePosition($tokens);
        $namespaceSemicolonPosition = $tokens->getNextTokenOfKind($namespacePosition, [';']);

        $tokens->insertAt($namespaceSemicolonPosition + 1, [
           new Token([T_USE, 'use']),
           new Token([T_WHITESPACE, PHP_EOL . ' ']),
           new Token([T_STRING, $useStatement]),
        ]);
    }
}
