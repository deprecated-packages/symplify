<?php

declare(strict_types=1);

/**
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\SymfonyEventDispatcher\Event;

use Nette\Application\Application;
use Symfony\Component\EventDispatcher\Event;
use Throwable;

final class ApplicationExceptionEvent extends Event
{
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
