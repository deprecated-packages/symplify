<?php

declare(strict_types=1);

/**
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\Event;

use Nette\Application\Application;
use Symfony\Component\EventDispatcher\Event;

final class ApplicationEvent extends Event
{
    /**
     * The ON_STARTUP event occurs before the application loads presenter.
     *
     * @see \Nette\Application\Application::$onStartup
     *
     * @var string
     */
    const ON_STARTUP = Application::class . '::onStartup';

    /**
     * @var Application
     */
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function getApplication() : Application
    {
        return $this->application;
    }
}
