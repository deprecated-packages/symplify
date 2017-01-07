<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\Event;

use Nette\Application\Application;
use Nette\Application\Request;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event occurs when a new request is received.
 *
 * @see \Nette\Application\Application::$onRequest
 */
final class RequestRecievedEvent extends Event
{
    /**
     * @var string
     */
    const NAME = Application::class . '::$onRequest';

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
