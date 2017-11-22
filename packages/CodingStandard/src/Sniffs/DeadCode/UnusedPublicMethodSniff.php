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
     * @var mixed[]
     */
    private $tokens = [];

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
        $this->tokens = $file->getTokens();
        $this->position = $position;

        if ($this->runNumber === 1) {
            $this->collectPublicMethodNames();
            $this->collectMethodCalls();
        }

        if ($this->runNumber === 2) {
            $unusedMethodNames = array_diff($this->publicMethodNames, $this->calledMethodNames);

            $this->checkUnusedPublicMethods($unusedMethodNames);
        }
    }

    private function collectPublicMethodNames(): void
    {
        $token = $this->tokens[$this->position];

        if (! $this->isPublicMethodToken($token)) {
            return;
        }

        $methodNameToken = $this->tokens[$this->position + 2];

        $this->publicMethodNames[] = $methodNameToken['content'];
    }

    private function collectMethodCalls(): void
    {
        $token = $this->tokens[$this->position];

        if ($token['code'] !== T_OBJECT_OPERATOR) {
            return;
        }

        $openBracketToken = $this->tokens[$this->position + 2];
        if ($openBracketToken['content'] !== '(') {
            return;
        }

        $methodNameToken = $this->tokens[$this->position + 1];

        if ($methodNameToken['code'] !== T_STRING) {
            return;
        }

        $this->calledMethodNames[] = $methodNameToken['content'];
    }

    /**
     * @param string[] $unusedMethodNames
     */
    private function checkUnusedPublicMethods(array $unusedMethodNames): void
    {
        $token = $this->tokens[$this->position];

        if (! $this->isPublicMethodToken($token)) {
            return;
        }

        $methodNameToken = $this->tokens[$this->position + 2];
        $methodName = $methodNameToken['content'];

        if (! in_array($methodName, $unusedMethodNames)) {
            return;
        }

        $this->file->addError(self::MESSAGE, $this->position, self::class);
    }

    private function isPublicMethodToken(array $token): bool
    {
        if (! $token['code'] === T_FUNCTION) {
            return false;
        }

        // not a public function
        if ($this->tokens[$this->position - 2]['code'] !== T_PUBLIC) {
            return false;
        }

        $nextToken = $this->tokens[$this->position + 2];

        // is function with name
        return $nextToken['code'] === T_STRING;
    }

    public function increaseRun(): void
    {
        ++$this->runNumber;
    }
}
