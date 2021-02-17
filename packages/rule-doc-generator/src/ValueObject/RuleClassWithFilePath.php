<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\ValueObject;

final class RuleClassWithFilePath
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $path;

    public function __construct(string $class, string $path)
    {
        $this->class = $class;
        $this->path = $path;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
