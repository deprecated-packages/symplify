<?php

declare(strict_types=1);

/**
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\Event;

use Nette\Application\Application;
use Symfony\Component\EventDispatcher\Event;
use Throwable;

final class ApplicationExceptionEvent extends Event
{
    /**
     * The ON_SHUTDOWN event occurs before the application shuts down,.
     *
     * @see \Nette\Application\Application::$onShutdown
     *
     * @var string
     */
    const ON_SHUTDOWN = Application::class . '::onShutdown';

    /**
     * The ON_ERROR event when an unhandled exception occurs in the application,.
     *
     * @see \Nette\Application\Application::$onError
     *
     * @var string
     */
    const ON_ERROR = Application::class . '::onError';

    /**
     * @var Application
     */
    private $application;

    /**
     * @var Throwable|null
     */
    private $exception;

    public function __construct(Application $application, Throwable $exception = null)
    {
        $this->application = $application;
        $this->exception = $exception;
    }

    public function getApplication() : Application
    {
        return $this->application;
    }

    /**
     * @return Throwable|null
     */
    public function getException()
    {
        return $this->exception;
    }
}
