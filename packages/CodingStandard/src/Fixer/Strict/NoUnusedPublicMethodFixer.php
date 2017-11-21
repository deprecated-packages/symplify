<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Strict;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\EasyCodingStandard\Contract\Application\DualRunInterface;
use Symplify\TokenRunner\Wrapper\FixerWrapper\MethodWrapper;

/**
 * @experimental
 *
 * See https://stackoverflow.com/a/9979425/1348344
 *
 * Inspiration http://www.andreybutov.com/2011/08/20/how-do-i-find-unused-functions-in-my-php-project/
 */
final class NoUnusedPublicMethodFixer implements FixerInterface, DefinedFixerInterface, DualRunInterface
{
    /**
     * @var int
     */
    private $runNumber = 1;

    /**
     * @var string[]
     */
    private $publicMethodNames = [];

    /**
     * @var string[]
     */
    private $calledMethodNames = [];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'There should be no unused public methods.',
            [
                new CodeSample('
<?php
class SomeClass {
    public function someMethod()
    {
    }
}'),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_FUNCTION])
            && $tokens->isAnyTokenKindsFound([T_PUBLIC, T_OBJECT_OPERATOR]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        if ($this->runNumber === 1) {
            $this->collectPublicMethodNames($tokens);
            $this->collectMethodCalls($tokens);
        }

        if ($this->runNumber === 2) {
            $unusedMethodNames = array_diff($this->publicMethodNames, $this->calledMethodNames);

            $this->removeUnusedPublicMethods($tokens, $unusedMethodNames);
        }

        ++$this->runNumber;
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function collectPublicMethodNames(Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (! $this->isPublicMethodToken($tokens, $token, $index)) {
                continue;
            }

            $functionNameToken = $tokens[$tokens->getNextMeaningfulToken($index)];

            $this->publicMethodNames[] = $functionNameToken->getContent();
        }
    }

    private function collectMethodCalls(Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind(T_OBJECT_OPERATOR)) {
                continue;
            }

            $openBracketToken = $tokens[$tokens->getNextMeaningfulToken($index + 1)];
            if (! $openBracketToken->getContent() === '(') {
                continue;
            }

            $methodNameToken = $tokens[$tokens->getNextMeaningfulToken($index)];

            if (! $methodNameToken->isGivenKind(T_STRING)) {
                continue;
            }

            $this->calledMethodNames[] = $methodNameToken->getContent();
        }
    }

    /**
     * @param string[] $unusedMethodNames
     */
    private function removeUnusedPublicMethods(Tokens $tokens, array $unusedMethodNames): void
    {
        /** @var Token[] $reversedTokens */
        $reversedTokens = array_reverse(iterator_to_array($tokens), true);

        foreach ($reversedTokens as $index => $token) {
            if (! $this->isPublicMethodToken($tokens, $token, $index)) {
                continue;
            }

            $functionNameToken = $tokens[$tokens->getNextMeaningfulToken($index)];
            $functionName = $functionNameToken->getContent();

            if (! in_array($functionName, $unusedMethodNames, true)) {
                continue;
            }

            $methodWrapper = MethodWrapper::createFromTokensAndPosition($tokens, $index);

            $tokens->clearRange($methodWrapper->getMethodStart(), $methodWrapper->getBodyEnd());
        }
    }

    private function isPublicMethodToken(Tokens $tokens, Token $token, int $index): bool
    {
        if (! $token->isGivenKind(T_FUNCTION)) {
            return false;
        }

        if ($tokens[$index - 1]->isGivenKind(T_PUBLIC)) {
            return false;
        }

        $functionNameToken = $tokens[$tokens->getNextMeaningfulToken($index)];
        if (! $functionNameToken->isGivenKind(T_STRING)) {
            return false;
        }

        return true;
    }
}
