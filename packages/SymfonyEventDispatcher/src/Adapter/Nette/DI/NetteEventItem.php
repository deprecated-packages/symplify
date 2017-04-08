<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\DI;

final class NetteEventItem
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $eventClass;

    /**
     * @var string
     */
    private $eventName;

    public function __construct(string $class, string $property, string $eventClass, ?string $eventName = null)
    {
        $this->class = $class;
        $this->property = $property;
        $this->eventClass = $eventClass;
        $this->eventName = $eventName;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getEventClass(): string
    {
        return $this->eventClass;
    }

    public function getEventName(): string
    {
        if ($this->eventName) {
            return $this->eventName;
        }

        return $this->class . '::$' . $this->property;
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}
