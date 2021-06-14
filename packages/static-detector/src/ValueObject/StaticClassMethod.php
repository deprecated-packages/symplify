<?php

declare(strict_types=1);

namespace Symplify\StaticDetector\ValueObject;

use PhpParser\Node\Stmt\ClassMethod;

final class StaticClassMethod
{
    public function __construct(
        private string $class,
        private string $method,
        private ClassMethod $classMethod
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getFileLocationWithLine(): string
    {
        return $this->classMethod->getAttribute(StaticDetectorAttributeKey::FILE_LINE);
    }
}
