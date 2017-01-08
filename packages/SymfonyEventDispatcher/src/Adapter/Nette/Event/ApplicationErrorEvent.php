<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\Event;

use Nette\Application\Application;
use Symfony\Component\EventDispatcher\Event;
use Throwable;

/**
 * The ON_ERROR event when an unhandled exception occurs in the application.
 *
 * @see \Nette\Application\Application::$onError
 */
final class ApplicationErrorEvent extends Event
{
    /**
     * @var string
     */
    public const NAME = Application::class . '::$onError';

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

    public function getException() : ?Throwable
    {
        return $this->exception;
    }
}
