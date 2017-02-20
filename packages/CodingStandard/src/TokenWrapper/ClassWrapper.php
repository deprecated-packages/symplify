<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenWrapper;

use Nette\PhpGenerator\Method;
use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use Symplify\CodingStandard\Helper\TokenFinder;

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
     * @var array
     */
    private $classToken;

    /**
     * @var array
     */
    private $tokens;

    /**
     * @var array|MethodWrapper[]
     */
    private $methods;

    private function __construct(File $file, int $position)
    {
        $this->file = $file;
        $this->position = $position;

        $this->tokens = $this->file->getTokens();
        $this->classToken = $this->tokens[$position];
    }

    public static function createFromFileAndPosition(File $file, int $position)
    {
        return new self($file, $position);
    }

    public function getClassName() : string
    {
        $namePosition = $this->file->findNext(T_STRING, $this->position);

        return $this->tokens[$namePosition]['content'];
    }

    public function isAbstract() : bool
    {
        $classProperties = $this->file->getClassProperties($this->position);

        return $classProperties['is_abstract'];
    }

    public function hasNameSuffix(string $suffix) : bool
    {
        return Strings::contains($this->getClassName(), $suffix);
    }

    /**
     * @return PropertyWrapper[]
     */
    public function getProperties() : array
    {
        $properties = [];

        $classOpenerPosition = $this->classToken['scope_opener'] + 1;

        while (($propertyTokenPointer = $this->file->findNext(
            T_VARIABLE,
            $classOpenerPosition,
            $this->classToken['scope_closer'])) !== false
        ) {
            $classOpenerPosition = $propertyTokenPointer + 1;

            $properties[] = PropertyWrapper::createFromFileAndPosition($this->file, $propertyTokenPointer);
        }

        return $properties;
    }

    /**
     * @return MethodWrapper[]
     */
    public function getMethods() : array
    {
        if ($this->methods) {
            return $this->methods;
        }

        $methods = [];

        $classOpenerPosition = $this->classToken['scope_opener'] + 1;

        while (($methodTokenPointer = $this->file->findNext(
            T_FUNCTION,
            $classOpenerPosition,
            $this->classToken['scope_closer'])) !== false
        ) {
            $classOpenerPosition = $methodTokenPointer + 1;

            $methods[] = MethodWrapper::createFromFileAndPosition($this->file, $methodTokenPointer);
        }

        return $this->methods = $methods;
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

    public function addConstructorMethodWithProperty(string $propertyType, string $propertyName)
    {
        $method = $this->createConstructMethod();
        $method->addParameter($propertyName)
            ->setTypeHint($propertyType);
        $method->setBody('$this->? = $?;', [$propertyName, $propertyName]);

        $methodCode = Strings::indent((string) $method, 1, '    ');

        $constructorPosition = $this->getConstructorPosition();
        $this->file->fixer->addContentBefore($constructorPosition, PHP_EOL . $methodCode . PHP_EOL);
    }

    private function getConstructorPosition() : int
    {
        $lastPropertyPosition = null;
        foreach ($this->getProperties() as $property) {
            $lastPropertyPosition = $property->getPosition();
        }

        if ($lastPropertyPosition) {
            return TokenFinder::findNextLinePosition($this->file, $lastPropertyPosition);
        }
    }

    private function createConstructMethod() : Method
    {
        $method = new Method('__construct');
        $method->setVisibility('public');
        return $method;
    }
}
