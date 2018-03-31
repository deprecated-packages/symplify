<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\DeadCode;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\Contract\Application\DualRunInterface;
use Symplify\TokenRunner\Wrapper\SnifferWrapper\ClassWrapperFactory;

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
     * @var ClassWrapperFactory
     */
    private $classWrapperFactory;

    /**
     * @var string[]
     */
    private $unusedMethodNames = [];

    public function __construct(ClassWrapperFactory $classWrapperFactory)
    {
        $this->classWrapperFactory = $classWrapperFactory;
    }

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
            // already collected from this file => skip it
            if (count($this->publicMethodNames)) {
                return;
            }

            $this->collectPublicMethodNames($file);
            $this->collectMethodCalls();
            return;
        }

        if ($this->runNumber === 2) {
            // prepare unused method names if not ready
            if ($this->unusedMethodNames === []) {
                $this->publicMethodNames = array_unique($this->publicMethodNames);
                $this->unusedMethodNames = array_diff($this->publicMethodNames, $this->calledMethodNames);
            }

            if ($this->shouldSkipFile($file)) {
                return;
            }

            // every method name was used, nothing to check
            if ($this->unusedMethodNames === []) {
                return;
            }

            $this->checkUnusedPublicMethods();
        }
    }

    public function increaseRun(): void
    {
        ++$this->runNumber;
    }

    private function collectPublicMethodNames(File $file): void
    {
        // @todo: is this useful here?
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
            // starts with uppercase name, is not a method name
            if (ctype_upper($token['content'][0])) {
                return;
            }

            $this->publicMethodNames[] = $token['content'];
        }
    }

    private function checkUnusedPublicMethods(): void
    {
        $token = $this->tokens[$this->position];

        if (! $this->isPublicMethodToken($token)) {
            return;
        }

        $methodNameToken = $this->tokens[$this->position + 2];
        $methodName = $methodNameToken['content'];

        if (! in_array($methodName, $this->unusedMethodNames, true)) {
            return;
        }

        $this->file->addError(sprintf(self::MESSAGE, $methodName), $this->position, self::class);
    }

    private function isPublicMethodToken(array $token): bool
    {
        if ($token['code'] !== T_FUNCTION) {
            return false;
        }

        return (bool) $this->file->findPrevious(T_PUBLIC, $this->position, $this->position - 5);
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

        $classWrapper = $this->classWrapperFactory->createFromFirstClassInFile($file);
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
