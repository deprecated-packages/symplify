<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Configuration;

use Symplify\Statie\Generator\Contract\ObjectSorterInterface;
use Symplify\Statie\Generator\FileNameObjectSorter;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\File;

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
     * @var AbstractFile[]
     */
    private $objects = [];

    /**
     * @var ObjectSorterInterface
     */
    private $objectSorter;

    private function __construct(
        string $variable,
        string $variableGlobal,
        string $path,
        string $layout,
        string $routePrefix,
        string $object,
        ObjectSorterInterface $objectSorter
    ) {
        $this->variable = $variable;
        $this->variableGlobal = $variableGlobal;
        $this->path = $path;
        $this->layout = $layout;
        $this->routePrefix = $routePrefix;
        $this->object = $object;
        $this->objectSorter = $objectSorter;
    }

    /**
     * @param mixed[]|mixed $configuration
     */
    public static function createFromConfiguration($configuration): self
    {
        return new self(
            $configuration['variable'],
            $configuration['variable_global'],
            $configuration['path'],
            $configuration['layout'],
            $configuration['route_prefix'],
            $configuration['object'] ?? File::class,
            isset($configuration['object_sorter']) ? new $configuration['object_sorter']() : new FileNameObjectSorter()
        );
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
     * @param AbstractFile[] $objects
     */
    public function setObjects(array $objects): void
    {
        $this->objects = $objects;
    }

    /**
     * @return AbstractFile[]
     */
    public function getObjects(): array
    {
        return $this->objects;
    }

    public function getObjectSorter(): ObjectSorterInterface
    {
        return $this->objectSorter;
    }
}
