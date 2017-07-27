<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenWrapper;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;

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

    private function __construct(File $file, int $position)
    {
        $this->file = $file;
        $this->position = $position;

        $this->tokens = $this->file->getTokens();
        $this->classToken = $this->tokens[$position];
    }

    public static function createFromFileAndPosition(File $file, int $position): self
    {
        return new self($file, $position);
    }

    public function getClassName(): string
    {
        return $this->file->getDeclarationName($this->position);
    }

    public function isAbstract(): bool
    {
        $classProperties = $this->file->getClassProperties($this->position);

        return $classProperties['is_abstract'];
    }

    public function hasNameSuffix(string $suffix): bool
    {
        return Strings::contains($this->getClassName(), $suffix);
    }

    /**
     * @return PropertyWrapper[]
     */
    public function getProperties(): array
    {
        $properties = [];

        $classOpenerPosition = $this->classToken['scope_opener'] + 1;

        while (($propertyTokenPointer = $this->file->findNext(
            T_VARIABLE,
            $classOpenerPosition,
            $this->classToken['scope_closer']
        )) !== false
        ) {
            $classOpenerPosition = $propertyTokenPointer + 1;

            $properties[] = PropertyWrapper::createFromFileAndPosition($this->file, $propertyTokenPointer);
        }

        return $properties;
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
}
