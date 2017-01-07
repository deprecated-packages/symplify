<?php

declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\Event;

use Nette\Application\Application;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event occurs before the application loads presenter.
 *
 * @see \Nette\Application\Application::$onStartup
 */
final class ApplicationStartupEvent extends Event
{
    /**
     * @var string
     */
    const NAME = Application::class . '::$onStartup';

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
