<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\SnifferWrapper;

use PHP_CodeSniffer\Files\File;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use ReflectionClass;
use ReflectionProperty;
// @todo: remove deps
use SlevomatCodingStandard\Helpers\TokenHelper;
use Symplify\TokenRunner\Guard\TokenTypeGuard;
use Symplify\TokenRunner\Helper\Naming;

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
    private $interfaces = [];

    /**
     * @var string[]
     */
    private $propertyNames = [];

    private function __construct(File $file, int $position)
    {
        $this->file = $file;
        $this->position = $position;

        TokenTypeGuard::ensureIsTokenType($file->getTokens()[$position], [T_CLASS, T_TRAIT, T_INTERFACE], __METHOD__);

        $this->tokens = $file->getTokens();
        $this->classToken = $this->tokens[$position];
    }

    public static function createFromFileAndPosition(File $file, int $position): self
    {
        return new self($file, $position);
    }

    public function getClassName(): string
    {
        return Naming::getClassName($this->file, $this->position + 2);
    }

    public function isAbstract(): bool
    {
        $classProperties = $this->file->getClassProperties($this->position);

        return $classProperties['is_abstract'];
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

    /**
     * @return MethodWrapper[]
     */
    public function getPublicMethods(): array
    {
        $publicMethods = [];
        foreach ($this->getMethods() as $methodName => $method) {
            if ($method->isPublic()) {
                $publicMethods[$methodName] = $method;
            }
        }

        unset($publicMethods['__construct']);

        return $publicMethods;
    }

    /**
     * @return false|MethodWrapper
     */
    public function getMethod(string $name)
    {
        foreach ($this->getMethods() as $methodName => $methodWrapper) {
            if ($methodName === $name) {
                return $methodWrapper;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    public function getInterfacesRequiredMethods(): array
    {
        $interfaceMethods = [];
        foreach ($this->getInterfaces() as $interface) {
            $interfaceMethods = array_merge($interfaceMethods, get_class_methods($interface));
        }

        return $interfaceMethods;
    }

    /**
     * @return string[]
     */
    public function getInterfaces(): array
    {
        if (empty($this->interfaces)) {
            $class = $this->getClassFullyQualifiedName();
            if (class_exists($class)) {
                $this->interfaces = class_implements($class);
            }
        }

        return $this->interfaces;
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
     * @return false|int
     */
    public function getLineDistanceBetweenClassAndLastUseStatement()
    {
        $lastUseStatementPosition = $this->getLastUseStatementPosition();
        if (! $lastUseStatementPosition) {
            return false;
        }

        return (int) $this->tokens[$this->getClassPositionIncludingComment()]['line']
            - $this->tokens[$lastUseStatementPosition]['line']
            - 1;
    }

    /**
     * @return bool|int
     */
    public function getLastUseStatementPosition()
    {
        return $this->file->findPrevious(T_USE, $this->position);
    }

    /**
     * @return bool|int
     */
    public function getLineDistanceBetweenNamespaceAndFirstUseStatement()
    {
        $namespacePosition = (int) $this->file->findPrevious(T_NAMESPACE, $this->position);

        $nextUseStatementPosition = (int) $this->file->findNext(T_USE, $namespacePosition);
        if (! $nextUseStatementPosition) {
            return false;
        }

        if ($this->tokens[$nextUseStatementPosition]['line'] === 1 || $this->isInsideClass($nextUseStatementPosition)) {
            return false;
        }

        return $this->tokens[$nextUseStatementPosition]['line'] - $this->tokens[$namespacePosition]['line'] - 1;
    }

    /**
     * @return false|int
     */
    public function getLineDistanceBetweenClassAndNamespace()
    {
        $namespacePosition = $this->file->findPrevious(T_NAMESPACE, $this->position);

        if (! $namespacePosition) {
            return false;
        }

        $classStartPosition = $this->getClassPositionIncludingComment();

        return $this->tokens[$classStartPosition]['line'] - $this->tokens[$namespacePosition]['line'] - 1;
    }

    /**
     * @return bool|int
     */
    private function getClassPositionIncludingComment()
    {
        $classStartPosition = $this->file->findPrevious(T_DOC_COMMENT_OPEN_TAG, $this->position);
        if ($classStartPosition) {
            return $classStartPosition;
        }

        return $this->position;
    }

    private function isInsideClass(int $position): bool
    {
        $prevClassPosition = $this->file->findPrevious(T_CLASS, $position, null, false);
        if ($prevClassPosition) {
            return true;
        }

        return false;
    }

    private function getClassFullyQualifiedName(): string
    {
        $namespaceStart = $this->file->findNext([T_NAMESPACE], 0);
        $class = '';
        if ($namespaceStart !== false) {
            $namespaceEnd = (int) $this->file->findNext([T_SEMICOLON], $namespaceStart + 2);
            for ($i = $namespaceStart + 2; $i < $namespaceEnd; ++$i) {
                $class .= $this->tokens[$i]['content'];
            }

            $class .= '\\';
        } else {
            $namespaceEnd = 0;
        }

        $classPosition = (int) $this->file->findNext([T_CLASS, T_INTERFACE], $namespaceEnd);
        $class .= $this->file->getDeclarationName($classPosition);

        return $class;
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
