<?php

declare(strict_types=1);

/**
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\Event;

use Nette\Application\Application;
use Nette\Application\IPresenter;
use Symfony\Component\EventDispatcher\Event;

final class ApplicationPresenterEvent extends Event
{
    /**
     * The ON_PRESENTER event when a presenter is created.
     *
     * @see \Nette\Application\Application::$onPresenter.
     *
     * @var string
     */
    const ON_PRESENTER = Application::class . '::onPresenter';

    /**
     * @var Application
     */
    private $application;

    /**
     * @var IPresenter
     */
    private $presenter;

    public function __construct(Application $application, IPresenter $presenter)
    {
        $this->application = $application;
        $this->presenter = $presenter;
    }

    public function getApplication() : Application
    {
        return $this->application;
    }

    public function getPresenter() : IPresenter
    {
        return $this->presenter;
    }
}
