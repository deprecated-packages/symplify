<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class MethodCallReference
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $methodName;

    public function __construct(string $class, string $methodName)
    {
        $this->class = $class;
        $this->methodName = $methodName;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }
}
