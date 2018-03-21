<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\DeadCode;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\Contract\Application\DualRunInterface;
use Symplify\TokenRunner\Wrapper\SnifferWrapper\ClassWrapper;

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
        return [T_FUNCTION, T_OBJECT_OPERATOR, T_CONSTANT_ENCAPSED_STRING, T_STRING, T_DOUBLE_COLON];
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
            $this->collectPublicMethodNames($file);
            $this->collectMethodCalls();
        }

        if ($this->runNumber === 2) {
            if ($this->shouldSkipFile($file)) {
                return;
            }

            $this->publicMethodNames = array_unique($this->publicMethodNames);

            $unusedMethodNames = array_diff($this->publicMethodNames, $this->calledMethodNames);

            $this->checkUnusedPublicMethods($unusedMethodNames);
        }
    }

    public function increaseRun(): void
    {
        ++$this->runNumber;
    }

    private function collectPublicMethodNames(File $file): void
    {
        if ($this->shouldSkipFile($file)) {
            return;
        }

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

        if ($token['code'] === T_OBJECT_OPERATOR) {
            $this->processObjectOperatorToken($token);
        }

        // possible method call in string
        if ($token['code'] === T_CONSTANT_ENCAPSED_STRING) {
            // match method name
            if (Strings::match($token['content'], '#^\'[a-z]{1}[a-zA-Z]+\'$#')) {
                $this->calledMethodNames[] = trim($token['content'], '\'');
            }

            return;
        }

        // SomeClass::somemMethod
        if ($token['code'] === T_DOUBLE_COLON) {
            $nextToken = $this->tokens[$this->position + 1];
            if ($nextToken['code'] !== T_STRING) {
                return;
            }

            $this->calledMethodNames[] = $nextToken['content'];
        }

        if ($token['code'] === T_STRING) {
            $this->publicMethodNames[] = $token['content'];
        }
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

        $this->file->addError(sprintf(self::MESSAGE, $methodName), $this->position, self::class);
    }

    private function isPublicMethodToken(array $token): bool
    {
        if ($token['code'] !== T_FUNCTION) {
            return false;
        }

        $previousToken = $this->tokens[$this->position - 2];
        if ($previousToken['code'] === T_STATIC) {
            $prePreviousToken = $this->tokens[$this->position - 4];
            // not a public static function
            if ($prePreviousToken['code'] !== T_PUBLIC) {
                return false;
            }
        } elseif ($previousToken['code'] !== T_PUBLIC) {
            // not a public function
            return false;
        }

        $nextToken = $this->tokens[$this->position + 2];

        // is function with name
        return $nextToken['code'] === T_STRING;
    }

    /**
     * Skip tests as there might be many unused public methods
     *
     * Skip anything that implements interface or extends class,
     * because methods can be enforced by them.
     */
    private function shouldSkipFile(File $file): bool
    {
        if (Strings::contains($file->getFilename(), '/tests/')
            && ! Strings::contains($file->getFilename(), 'CodingStandard/tests/')
        ) {
            return true;
        }

        $classWrapper = ClassWrapper::createFromFirstClassInFile($file);
        if ($classWrapper === null) {
            return true;
        }

        return $classWrapper->implementsInterface() || $classWrapper->extends();
    }

    private function processObjectOperatorToken($token): void
    {
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
}
