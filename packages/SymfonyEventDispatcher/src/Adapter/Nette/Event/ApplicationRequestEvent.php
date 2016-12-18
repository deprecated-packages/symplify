<?php

declare(strict_types=1);

/**
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\Event;

use Nette\Application\Application;
use Nette\Application\Request;
use Symfony\Component\EventDispatcher\Event;

final class ApplicationRequestEvent extends Event
{
    /**
     * The ON_REQUEST event occurs when a new request is received,.
     *
     * @see \Nette\Application\Application::$onRequest
     *
     * @var string
     */
    const ON_REQUEST = Application::class . '::onRequest';

    /**
     * @var Application
     */
    private $application;

    /**
     * @var Request
     */
    private $request;

    public function __construct(Application $application, Request $request)
    {
        $this->application = $application;
        $this->request = $request;
    }

    public function getApplication() : Application
    {
        return $this->application;
    }

    public function getRequest() : Request
    {
        return $this->request;
    }
}
