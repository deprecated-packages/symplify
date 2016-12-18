<?php

declare(strict_types=1);

/**
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\Event;

use Nette\Application\IPresenter;
use Nette\Application\IResponse;
use Nette\Application\UI\Presenter;
use Symfony\Component\EventDispatcher\Event;

final class PresenterResponseEvent extends Event
{
    /**
     * The ON_SHUTDOWN event occurs when the presenter is shutting down.
     *
     * @see \Nette\Application\UI\Presenter::$onShutdown
     *
     * @var string
     */
    const ON_SHUTDOWN = Presenter::class . '::onShutdown';

    /**
     * @var IPresenter|Presenter
     */
    private $presenter;

    /**
     * @var IResponse
     */
    private $response;

    public function __construct(IPresenter $presenter, IResponse $response = null)
    {
        $this->presenter = $presenter;
        $this->response = $response;
    }

    public function getPresenter() : Presenter
    {
        return $this->presenter;
    }

    /**
     * @return IResponse|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
