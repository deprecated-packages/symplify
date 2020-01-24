<?php

declare(strict_types=1);

namespace Symplify\Statie\Generator\Configuration;

use Symplify\Statie\Generator\Contract\ObjectSorterInterface;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;

final class GeneratorElement
{
    /**
     * @var string
     */
    private $variable;

    /**
     * @var string
     */
    private $variableGlobal;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $layout;

    /**
     * @var string
     */
    private $routePrefix;

    /**
     * @var string
     */
    private $object;

    /**
     * @var bool
     */
    private $hasHeadlineAnchors = false;

    /**
     * @var AbstractGeneratorFile[]
     */
    private $objects = [];

    /**
     * @var ObjectSorterInterface
     */
    private $objectSorter;

    public function __construct(
        string $variable,
        string $variableGlobal,
        string $path,
        string $layout,
        string $routePrefix,
        string $object,
        ObjectSorterInterface $objectSorter,
        bool $hasHeadlineAnchors
    ) {
        $this->variable = $variable;
        $this->variableGlobal = $variableGlobal;
        $this->path = $path;
        $this->layout = $layout;
        $this->routePrefix = $routePrefix;
        $this->object = $object;
        $this->objectSorter = $objectSorter;
        $this->hasHeadlineAnchors = $hasHeadlineAnchors;
    }

    public function getVariable(): string
    {
        return $this->variable;
    }

    public function getVariableGlobal(): string
    {
        return $this->variableGlobal;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function getRoutePrefix(): string
    {
        return $this->routePrefix;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    /**
     * @param AbstractGeneratorFile[] $objects
     */
    public function setObjects(array $objects): void
    {
        $this->objects = $objects;
    }

    /**
     * @return AbstractGeneratorFile[]
     */
    public function getObjects(): array
    {
        return $this->objects;
    }

    public function getObjectSorter(): ObjectSorterInterface
    {
        return $this->objectSorter;
    }

    public function hasHeadlineAnchors(): bool
    {
        return $this->hasHeadlineAnchors;
    }
}
