<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\Event;

use Nette\Application\Application;
use Symfony\Component\EventDispatcher\Event;
use Throwable;

/**
 * This event occurs before the application shuts down.

 * @see \Nette\Application\Application::$onShutdown
 */
final class ApplicationShutdownEvent extends Event
{
    /**
     * @var string
     */
    const NAME = Application::class . '::$onShutdown';

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
