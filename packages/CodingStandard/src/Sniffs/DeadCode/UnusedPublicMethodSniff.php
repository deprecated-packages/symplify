<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\DeadCode;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
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
    private const MESSAGE = 'Public method "%s()" is probably unused.';

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
     * Fmmatch list of methods to ignore
     *
     * @var string[]
     */
    private $methodsToIgnore = [
        '__*',
        'test*',
        'provide*',
        'offset*',
    ];

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
        if ($this->shouldSkipFile($file)) {
            return;
        }

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

    public function increaseRun(): void
    {
        ++$this->runNumber;
    }

    private function collectPublicMethodNames(): void
    {
        $token = $this->tokens[$this->position];

        if (! $this->isPublicMethodToken($token)) {
            return;
        }

        $methodNameToken = $this->tokens[$this->position + 2];
        $methodName = $methodNameToken['content'];

        if (Strings::match($methodName, sprintf('#^(%s)#', implode('|', $this->methodsToIgnore)))) {
            return;
        }

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

        if (! in_array($methodName, $unusedMethodNames, true)) {
            return;
        }

        $this->file->addError(sprintf(
            self::MESSAGE,
            $methodName
        ), $this->position, self::class);
    }

    private function isPublicMethodToken(array $token): bool
    {
        if ($token['code'] !== T_FUNCTION) {
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

    /**
     * Skip tests as there might be many unused public methods.
     */
    private function shouldSkipFile(File $file): bool
    {
        return Strings::contains($file->getFilename(), '/tests/') && ! Strings::contains($file->getFilename(), 'CodingStandard/tests/');
    }
}
