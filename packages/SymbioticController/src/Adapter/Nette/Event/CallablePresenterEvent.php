<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Adapter\Nette\Event;

use Nette\Application\Application;
use Nette\Application\IPresenter;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event occurs when a presenter is created.
 *
 * @see \Nette\Application\Application::$onPresenter
 */
final class CallablePresenterEvent extends Event
{
    /**
     * @var string
     */
    public const NAME = Application::class . '::$onPresenter';

    /**
     * @var Application
     */
    private $application;

    /**
     * @var callable|IPresenter
     */
    private $presenter;

    /**
     * @param callable|IPresenter $presenter
     */
    public function __construct(Application $application, $presenter)
    {
        $this->application = $application;
        $this->presenter = $presenter;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @return callable|IPresenter
     */
    public function getPresenter()
    {
        return $this->presenter;
    }
}
