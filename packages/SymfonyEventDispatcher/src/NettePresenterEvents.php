<?php

declare(strict_types=1);

/**
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\SymfonyEventDispatcher;

use Nette\Application\UI\Presenter;

/**
 * Events in Nette Presenter life cycle.
 */
final class NettePresenterEvents
{
    /**
     * The ON_SHUTDOWN event occurs when the presenter is shutting down,.
     *
     * @see \Nette\Application\UI\Presenter::$onShutdown.
     *
     * The event listener method receives a @see \Symplify\SymfonyEventDispatcher\Event\PresenterResponseEvent instance
     *
     * @var string
     */
    const ON_SHUTDOWN = Presenter::class . '::onShutdown';
}
