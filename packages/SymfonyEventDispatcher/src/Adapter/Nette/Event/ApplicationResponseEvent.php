<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\Event;

use Nette\Application\Application;
use Nette\Application\IResponse;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event occurs when a new response is ready for dispatch.
 *
 * @see \Nette\Application\Application::$onResponse
 */
final class ApplicationResponseEvent extends Event
{
    /**
     * @var string
     */
    const NAME = Application::class . '::$onResponse';

    /**
     * @var Application
     */
    private $application;

    /**
     * @var IResponse
     */
    private $response;

    public function __construct(Application $application, IResponse $response)
    {
        $this->application = $application;
        $this->response = $response;
    }

    public function getApplication() : Application
    {
        return $this->application;
    }

    public function getResponse() : IResponse
    {
        return $this->response;
    }
}
