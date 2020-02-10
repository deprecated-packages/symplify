<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\ValueObject;

final class ServiceConfig
{
    /**
     * @var bool
     */
    private $isAutowire = false;

    /**
     * @var bool
     */
    private $isAutoconfigure = false;

    /**
     * @var string[]
     */
    private $classes = [];

    /**
     * @param string[] $classes
     */
    public function __construct(array $classes = [])
    {
        $this->classes = $classes;
    }

    public function addClass(string $class): void
    {
        $this->classes[] = $class;
    }

    public function enableAutoconfigure(): void
    {
        $this->isAutoconfigure = true;
    }

    public function enableAutowire(): void
    {
        $this->isAutowire = true;
    }

    public function isAutoconfigure(): bool
    {
        return $this->isAutoconfigure;
    }

    public function isAutowire(): bool
    {
        return $this->isAutowire;
    }

    /**
     * @return string[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }
}
