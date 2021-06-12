<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class ClassConstantReference
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $constant;

    public function __construct(string $class, string $constant)
    {
        $this->class = $class;
        $this->constant = $constant;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getConstant(): string
    {
        return $this->constant;
    }
}
