<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\ValueObject;

use Nette\Utils\Strings;
use ReflectionMethod;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class ClassMethodName
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $method;

    /**
     * @var SmartFileInfo
     */
    private $latteFileInfo;

    public function __construct(string $class, string $method, SmartFileInfo $latteFileInfo)
    {
        $this->class = $class;
        $this->method = $method;
        $this->latteFileInfo = $latteFileInfo;
    }

    public function getClassMethodName(): string
    {
        return $this->class . '::' . $this->method;
    }

    public function getFileLine(): string
    {
        if ($this->isOnVariableStaticCall()) {
            throw new ShouldNotHappenException();
        }

        $reflectionMethod = $this->getReflectionMethod();
        return $reflectionMethod->getFileName() . ':' . $reflectionMethod->getStartLine();
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getFilterProviderClassName(): string
    {
        return ucfirst($this->method) . 'FilterProvider';
    }

    public function isOnVariableStaticCall(): bool
    {
        return Strings::startsWith($this->class, '$');
    }

    public function getReflectionMethod(): ReflectionMethod
    {
        if ($this->isOnVariableStaticCall()) {
            throw new ShouldNotHappenException();
        }

        return new ReflectionMethod($this->class, $this->method);
    }

    public function getLatteFilePath(): string
    {
        return $this->latteFileInfo->getRelativeFilePathFromCwd();
    }
}
