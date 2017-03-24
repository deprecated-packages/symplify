<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Application;

use Nette;
use Nette\Application\InvalidPresenterException;
use Nette\Application\IPresenter;
use Nette\Application\IPresenterFactory;
use Nette\DI\Container;
use ReflectionClass;

final class PresenterFactory implements IPresenterFactory
{
    /**
     * @var string[][] of module => splited mask
     */
    private $mapping = [
        '*' => ['', '*Module\\', '*Presenter'],
        'Nette' => ['NetteModule\\', '*\\', '*Presenter'],
    ];

    /**
     * @var string[]
     */
    private $cache = [];

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     * @return IPresenter|callable|object
     */
    public function createPresenter($name)
    {
        $presenterClass = $this->getPresenterClass($name);
        $presenter = $this->container->createInstance($presenterClass);

        if ($presenter instanceof Nette\Application\UI\Presenter) {
            $this->container->callInjects($presenter);
        }

        return $presenter;
    }

    /**
     * Generates and checks presenter class name.
     * @param string $name presenter name
     */
    public function getPresenterClass(&$name): string
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        if (! is_string($name) || ! Nette\Utils\Strings::match($name, '#^[a-zA-Z\x7f-\xff][a-zA-Z0-9\x7f-\xff:]*\z#')) {
            throw new InvalidPresenterException("Presenter name must be alphanumeric string, '$name' is invalid.");
        }

        $class = $this->formatPresenterClass($name);
        if (! class_exists($class)) {
            throw new InvalidPresenterException("Cannot load presenter '$name', class '$class' was not found.");
        }

        $reflection = new ReflectionClass($class);
        $class = $reflection->getName();

        if ($reflection->isAbstract()) {
            throw new InvalidPresenterException("Cannot load presenter '$name', class '$class' is abstract.");
        }

        $this->cache[$name] = $class;

        return $class;
    }

    /**
     * @param string[][]
     */
    public function setMapping(array $mapping): void
    {
        foreach ($mapping as $module => $mask) {
            if (is_string($mask)) {
                if (! preg_match('#^\\\\?([\w\\\\]*\\\\)?(\w*\*\w*?\\\\)?([\w\\\\]*\*\w*)\z#', $mask, $m)) {
                    throw new Nette\InvalidStateException("Invalid mapping mask '$mask'.");
                }
                $this->mapping[$module] = [$m[1], $m[2] ?: '*Module\\', $m[3]];
            } elseif (is_array($mask) && count($mask) === 3) {
                $this->mapping[$module] = [$mask[0] ? $mask[0] . '\\' : '', $mask[1] . '\\', $mask[2]];
            } else {
                throw new Nette\InvalidStateException("Invalid mapping mask for module $module.");
            }
        }
    }

    /**
     * Formats presenter class name from its name.
     */
    public function formatPresenterClass(string $presenter): string
    {
        $parts = explode(':', $presenter);
        $mapping = isset($parts[1], $this->mapping[$parts[0]])
            ? $this->mapping[array_shift($parts)]
            : $this->mapping['*'];

        while ($part = array_shift($parts)) {
            $mapping[0] .= str_replace('*', $part, $mapping[$parts ? 1 : 2]);
        }
        return $mapping[0];
    }
}
