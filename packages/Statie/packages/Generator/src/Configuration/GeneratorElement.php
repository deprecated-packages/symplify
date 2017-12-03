<?php declare(strict_types=1);

namespace Symplify\Statie\Generator\Configuration;

final class GeneratorElement
{
    /**
     * @var string
     */
    private $variable;

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

    public function __construct(string $variable, string $path, string $layout, string $routePrefix, string $object)
    {
        $this->variable = $variable;
        $this->path = $path;
        $this->layout = $layout;
        $this->routePrefix = $routePrefix;
        $this->object = $object;
    }

    /**
     * @param mixed[]|mixed $configuration
     */
    public static function createFromConfiguration($configuration): self
    {
        GeneratorElementGuard::ensureInputIsValid($configuration);

        return new self(
            $configuration['variable'],
            $configuration['path'],
            $configuration['layout'],
            $configuration['route_prefix'],
            $configuration['object']
        );
    }

    public function getVariable(): string
    {
        return $this->variable;
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
}
