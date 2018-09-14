<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\DeadCode;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\Contract\Application\DualRunInterface;
use Symplify\TokenRunner\Wrapper\SnifferWrapper\ClassWrapperFactory;
use function Safe\sprintf;

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
    private const MESSAGE = 'Public method "%s()" is possibly unused.';

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
    private $methodsToIgnore = ['__*', 'test*', 'provide*', 'offset*'];

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
            $this->collectPublicMethodNames($position);
            $this->collectMethodCalls();
            return;
        }

        if ($this->runNumber === 2) {
            // prepare unused method names if not ready
            if ($this->unusedMethodNames === []) {
                $this->unusedMethodNames = array_diff(
                    array_unique($this->publicMethodNames),
                    $this->calledMethodNames
                );
            }

            if ($this->shouldSkipFile($file)) {
                return;
            }

            $this->checkUnusedPublicMethods();
        }
    }

    public function increaseRun(): void
    {
        ++$this->runNumber;
    }

    private function collectPublicMethodNames(int $position): void
    {
        $token = $this->tokens[$position];
        if (! $this->isPublicMethodToken($token)) {
            return;
        }

        $possibleMethodNameToken = $this->findNextStringToken($position);
        if ($possibleMethodNameToken === null) {
            return;
        }

        $methodName = $possibleMethodNameToken['content'];
        if (Strings::match($methodName, sprintf('#^(%s)#', implode('|', $this->methodsToIgnore)))) {
            return;
        }

        $this->publicMethodNames[] = $methodName;
    }

    private function collectMethodCalls(): void
    {
        $token = $this->tokens[$this->position];

        // "->"
        if ($token['code'] === T_OBJECT_OPERATOR) {
            $this->processObjectOperatorToken();
            return;
        }

        $this->collectMethodNames($token);

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

    /**
     * @param mixed[] $token
     */
    private function isPublicMethodToken(array $token): bool
    {
        if ($token['code'] !== T_FUNCTION) {
            return false;
        }

        return (bool) $this->file->findPrevious(T_PUBLIC, $this->position, $this->position - 6);
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

    private function processObjectOperatorToken(): void
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

    /**
     * @return mixed[]|null
     */
    private function findNextStringToken(int $position): ?array
    {
        $possibleNextStringPosition = $this->file->findNext(T_STRING, $position + 1, $position + 3);
        if ($possibleNextStringPosition === false) {
            return null;
        }

        return $this->tokens[$possibleNextStringPosition];
    }

    /**
     * @param mixed[] $token
     */
    private function collectMethodNames(array $token): void
    {
        // possible method call in string
        if ($token['code'] === T_CONSTANT_ENCAPSED_STRING) {
            // match method name
            if (Strings::match($token['content'], '#^\'[a-z]{1}[a-zA-Z]+\'$#')) {
                $this->calledMethodNames[] = trim($token['content'], '\'');
            }
        }

        // SomeClass::"someMethod"
        if ($token['code'] === T_DOUBLE_COLON) {
            $nextToken = $this->tokens[$this->position + 1];
            if ($nextToken['code'] !== T_STRING) {
                return;
            }

            $this->calledMethodNames[] = $nextToken['content'];
        }
    }
}
