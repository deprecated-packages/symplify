<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\DeadCode;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\EasyCodingStandard\Contract\Application\DualRunInterface;

/**
 * @experimental
 *
 * See https://stackoverflow.com/a/9979425/1348344
 *
 * Inspiration http://www.andreybutov.com/2011/08/20/how-do-i-find-unused-functions-in-my-php-project/
 */
final class UnusedPublicMethodSniff implements Sniff, DualRunInterface
{
    /**
     * @var string
     */
    private const MESSAGE = 'There should be no unused public methods.';

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

    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_FUNCTION, T_OBJECT_OPERATOR];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->position = $position;

        if ($this->runNumber === 1) {
            $this->collectPublicMethodNames();
            $this->collectMethodCalls();
        }

        if ($this->runNumber === 2) {
            $unusedMethodNames = array_diff($this->publicMethodNames, $this->calledMethodNames);

            $this->removeUnusedPublicMethods($tokens, $unusedMethodNames);
        }

        ++$this->runNumber;
    }

    private function collectPublicMethodNames(): void
    {
        // ... $this->file

        foreach ($tokens as $index => $token) {
            if (! $this->isPublicMethodToken($tokens, $token, $index)) {
                continue;
            }

            $functionNameToken = $tokens[$tokens->getNextMeaningfulToken($index)];

            $this->publicMethodNames[] = $functionNameToken->getContent();
        }
    }

    private function collectMethodCalls(): void
    {
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind(T_OBJECT_OPERATOR)) {
                continue;
            }

            $openBracketToken = $tokens[$tokens->getNextMeaningfulToken($index + 1)];
            if ($openBracketToken->getContent() !== '(') {
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

            $file->addError(self::MESSAGE, '...');
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
