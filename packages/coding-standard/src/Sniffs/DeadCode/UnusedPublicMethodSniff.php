<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\DeadCode;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\TokenRunner\Analyzer\SnifferAnalyzer\Naming;
use Symplify\CodingStandard\TokenRunner\Wrapper\SnifferWrapper\SniffClassWrapperFactory;
use Symplify\EasyCodingStandard\Configuration\Contract\ResettableInterface;
use Symplify\EasyCodingStandard\Contract\Application\DualRunInterface;

/**
 * @experimental
 *
 * See https://stackoverflow.com/a/9979425/1348344
 */
final class UnusedPublicMethodSniff implements Sniff, DualRunInterface, ResettableInterface
{
    /**
     * Fmmatch list of methods to ignore
     *
     * @var string[]
     */
    private const METHODS_TO_IGNORE = ['__*', 'test*', 'provide*', 'offset*'];

    /**
     * Classes allowed to have unused public methods
     *
     * @var string[]
     */
    public $allowClasses = [];

    /**
     * @var int
     */
    private $runNumber = 1;

    /**
     * @var int
     */
    private $position;

    /**
     * @var string[]
     */
    private $publicMethodNames = [];

    /**
     * @var string[]
     */
    private $calledMethodNames = [];

    /**
     * @var mixed[]
     */
    private $tokens = [];

    /**
     * @var string[]
     */
    private $unusedMethodNames = [];

    /**
     * @var File
     */
    private $file;

    /**
     * @var SniffClassWrapperFactory
     */
    private $sniffClassWrapperFactory;

    /**
     * @var Naming
     */
    private $naming;

    public function __construct(SniffClassWrapperFactory $sniffClassWrapperFactory, Naming $naming)
    {
        $this->sniffClassWrapperFactory = $sniffClassWrapperFactory;
        $this->naming = $naming;
    }

    public function reset(): void
    {
        if (defined('PHPUNIT_COMPOSER_INSTALL') || defined('__PHPUNIT_PHAR__')) {
            $this->publicMethodNames = [];
            $this->calledMethodNames = [];
            $this->runNumber = 1;
        }
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
            $this->collectMethodCalls();
            $class = $this->naming->getFileClassName($this->file);
            if ($class && $this->shouldSkipClass($class)) {
                return;
            }

            $this->collectPublicMethodNames($position);
            return;
        }

        if ($this->runNumber === 2) {
            // prepare unused method names if not ready
            if ($this->unusedMethodNames === []) {
                $uniquePublicMethodNames = array_unique($this->publicMethodNames);
                sort($uniquePublicMethodNames);

                $this->unusedMethodNames = array_diff($uniquePublicMethodNames, $this->calledMethodNames);
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
        if ($this->shouldSkipMethod($methodName)) {
            return;
        }

        $this->publicMethodNames[] = $methodName;
    }

    private function collectMethodCalls(): void
    {
        // skip test classes, since they provide false usage
        $fileClassName = $this->naming->getFileClassName($this->file);
        if ($fileClassName && Strings::contains($fileClassName, 'Test')) {
            return;
        }

        $token = $this->tokens[$this->position];

        // "->"
        if ($token['code'] === T_OBJECT_OPERATOR) {
            $this->processObjectOperatorToken();
            return;
        }

        $this->collectMethodNames($token);
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

        $classWrapper = $this->sniffClassWrapperFactory->createFromFirstClassInFile($file);
        if ($classWrapper === null) {
            return true;
        }

        return $classWrapper->doesImplementInterface() || $classWrapper->doesExtendClass();
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

        $this->file->addError(sprintf(
            'Public method "%s()" is possibly unused.',
            $methodName
        ), $this->position, self::class);
    }

    /**
     * @param mixed[] $token
     */
    private function isPublicMethodToken(array $token): bool
    {
        if ($token['code'] !== T_FUNCTION) {
            return false;
        }

        $previousPosition = $this->position - 6;
        if (! isset($this->tokens[$previousPosition])) {
            return false;
        }

        return (bool) $this->file->findPrevious(T_PUBLIC, $this->position, $previousPosition);
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
     * @param mixed[] $token
     */
    private function collectMethodNames(array $token): void
    {
        // possible method call in string
        if ($token['code'] === T_CONSTANT_ENCAPSED_STRING && Strings::match(
            $token['content'],
            '#^\'[a-z]{1}[a-zA-Z]+\'$#'
        )) {
            $this->calledMethodNames[] = trim($token['content'], '\'');
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

    private function shouldSkipClass(string $class): bool
    {
        if (in_array($class, $this->allowClasses, true)) {
            return true;
        }

        // is doctrine entity?
        $fileContent = $this->file->getTokensAsString(1, count($this->file->getTokens()));
        if (Strings::contains($fileContent, '@ORM\Entity')) {
            return true;
        }
        // is controller, listener or subscriber, so unrecorded public methods are expected
        return (bool) Strings::match($fileContent, '#class\s+[\w]+(Controller|Listener|Subscriber)#');
    }

    private function shouldSkipMethod($methodName): bool
    {
        return (bool) Strings::match($methodName, sprintf('#^(%s)#', implode('|', self::METHODS_TO_IGNORE)));
    }
}
