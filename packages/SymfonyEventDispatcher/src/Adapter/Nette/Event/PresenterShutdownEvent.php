<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\Event;

use Nette\Application\IResponse;
use Nette\Application\UI\Presenter;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event occurs when the presenter is shutting down.
 *
 * @see \Nette\Application\UI\Presenter::$onShutdown
 */
final class PresenterShutdownEvent extends Event
{
    /**
     * @var string
     */
    public const NAME = Presenter::class . '::$onShutdown';

    /**
     * @var Presenter
     */
    private $presenter;

    /**
     * @var IResponse
     */
    private $response;

    public function __construct(Presenter $presenter, ?IResponse $response)
    {
        $this->presenter = $presenter;
        $this->response = $response;
    }

    public function getPresenter() : Presenter
    {
        return $this->presenter;
    }

    public function getResponse() : ?IResponse
    {
        return $this->response;
    }
}
