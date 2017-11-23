<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\SnifferWrapper;

use PHP_CodeSniffer\Files\File;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use ReflectionClass;
use ReflectionProperty;
use SlevomatCodingStandard\Helpers\TokenHelper;
use Symplify\TokenRunner\Analyzer\SnifferAnalyzer\Naming;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

final class ClassWrapper
{
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
    private $classToken = [];

    /**
     * @var mixed[]
     */
    private $tokens = [];

    /**
     * @var MethodWrapper[]
     */
    private $methods = [];

    /**
     * @var string[]
     */
    private $propertyNames = [];

    private function __construct(File $file, int $position)
    {
        TokenTypeGuard::ensureIsTokenType($file->getTokens()[$position], [T_CLASS, T_TRAIT, T_INTERFACE], __METHOD__);

        $this->file = $file;
        $this->position = $position;

        $this->tokens = $file->getTokens();
        $this->classToken = $this->tokens[$position];
    }

    public static function createFromFileAndPosition(File $file, int $position): self
    {
        // consider static cache factory
        return new self($file, $position);
    }

    public static function createFromFirstClassInFile(File $file): ?self
    {
        $possibleClassPosition = $file->findNext(T_CLASS, 0);
        if ($possibleClassPosition === false) {
            return null;
        }

        return self::createFromFileAndPosition($file, $possibleClassPosition);
    }

    public function getClassName(): string
    {
        return Naming::getClassName($this->file, $this->position + 2);
    }

    public function implementsInterface(): bool
    {
        return (bool) $this->file->findNext(T_IMPLEMENTS, $this->position, $this->position + 15);
    }

    public function extends(): bool
    {
        return (bool) $this->file->findNext(T_EXTENDS, $this->position, $this->position + 5);
    }

    /**
     * Inspired by @see TokensAnalyzer.
     *
     * @return string[]
     */
    public function getPropertyNames(): array
    {
        if ($this->propertyNames) {
            return $this->propertyNames;
        }

        $classOpenerPosition = $this->classToken['scope_opener'] + 1;
        $classCloserPosition = $this->classToken['scope_closer'] - 1;

        $propertyTokens = $this->findClassLevelTokensType($classOpenerPosition, $classCloserPosition, T_VARIABLE);

        $this->propertyNames = $this->extractPropertyNamesFromPropertyTokens($propertyTokens);
        $this->propertyNames = array_merge($this->propertyNames, $this->getParentClassPropertyNames());

        return $this->propertyNames;
    }

    /**
     * @return MethodWrapper[]
     */
    public function getMethods(): array
    {
        if (count($this->methods)) {
            return $this->methods;
        }

        $methods = [];
        $classOpenerPosition = $this->classToken['scope_opener'] + 1;

        while (($methodTokenPointer = $this->file->findNext(
            T_FUNCTION,
            $classOpenerPosition,
            $this->classToken['scope_closer']
        )) !== false
        ) {
            $classOpenerPosition = $methodTokenPointer + 1;

            $method = MethodWrapper::createFromFileAndPosition($this->file, $methodTokenPointer);
            $methods[$method->getName()] = $method;
        }

        return $this->methods = $methods;
    }

    public function getParentClassName(): ?string
    {
        $extendsTokenPosition = TokenHelper::findNext($this->file, T_EXTENDS, $this->position, $this->position + 10);
        if ($extendsTokenPosition === null) {
            return null;
        }

        $parentClassPosition = (int) TokenHelper::findNext($this->file, T_STRING, $extendsTokenPosition);

        return Naming::getClassName($this->file, $parentClassPosition);
    }

    /**
     * @return string[]
     */
    private function getParentClasses(): array
    {
        $firstParentClass = $this->getParentClassName();
        if ($firstParentClass === null || $firstParentClass === '') {
            return [];
        }

        return array_merge([$firstParentClass], class_parents($firstParentClass));
    }

    /**
     * @return string[]
     */
    private function getParentClassPropertyNames(): array
    {
        $parentClassPropertyNames = [];

        foreach ($this->getParentClasses() as $parentClass) {
            $parentClassPropertyNames = array_merge(
                $parentClassPropertyNames,
                $this->getPublicAndProtectedPropertyNamesFromClass($parentClass)
            );
        }

        return $parentClassPropertyNames;
    }

    /**
     * @return string[]
     */
    private function getPublicAndProtectedPropertyNamesFromClass(string $class): array
    {
        $propertyNames = [];

        $propertyReflections = (new ReflectionClass($class))->getProperties(
            ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED
        );

        foreach ($propertyReflections as $propertyReflection) {
            $propertyNames[] = $propertyReflection->getName();
        }

        return $propertyNames;
    }

    /**
     * @return mixed[]
     */
    private function findClassLevelTokensType(int $classOpenerPosition, int $classCloserPosition, int $type): array
    {
        $curlyBracesLevel = 0;
        $bracesLevel = 0;
        $foundTokens = [];

        // find properties inside class scope (between { and } of the class)
        for ($i = $classOpenerPosition; $i < $classCloserPosition; ++$i) {
            $token = $this->tokens[$i];

            // into arguments
            if ($token['content'] === '(') {
                ++$bracesLevel;

                continue;
            }

            // out of arguments
            if ($token['content'] === ')') {
                --$bracesLevel;

                continue;
            }

            // into method or function
            if ($token['content'] === '{') {
                ++$curlyBracesLevel;

                continue;
            }

            // out of method or function
            if ($token['content'] === '}') {
                --$curlyBracesLevel;

                continue;
            }

            // not in class level
            if ($curlyBracesLevel !== 0 || $bracesLevel !== 0) {
                continue;
            }

            if ($token['code'] === $type) {
                $foundTokens[$i] = $token;
            }
        }

        return $foundTokens;
    }

    /**
     * @param mixed[] $propertyTokens
     * @return string[]
     */
    private function extractPropertyNamesFromPropertyTokens(array $propertyTokens): array
    {
        $propertyNames = [];

        foreach ($propertyTokens as $propertyTokenPointer => $propertyToken) {
            $propertyToken = $this->tokens[$propertyTokenPointer];
            $propertyNames[] = ltrim($propertyToken['content'], '$');
        }

        return $propertyNames;
    }
}
