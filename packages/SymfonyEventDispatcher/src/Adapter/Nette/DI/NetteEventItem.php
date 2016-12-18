<?php

declare(strict_types=1);

/**
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz).
 */

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

    public function __construct(string $class, string $property, string $eventClass, string $eventName)
    {
        $this->class = $class;
        $this->property = $property;
        $this->eventClass = $eventClass;
        $this->eventName = $eventName;
    }

    public function getClass() : string
    {
        return $this->class;
    }

    public function getEventClass() : string
    {
        return $this->eventClass;
    }

    public function getEventName() : string
    {
        return $this->eventName;
    }

    public function getProperty() : string
    {
        return $this->property;
    }
}
