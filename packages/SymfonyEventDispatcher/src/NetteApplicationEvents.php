<?php

declare(strict_types=1);

/**
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\SymfonyEventDispatcher;

use Nette\Application\Application;

/**
 * Events in Nette Application life cycle.
 */
final class NetteApplicationEvents
{
    /**
     * The ON_STARTUP event occurs before the application loads presenter,.
     *
     * @see \Nette\Application\Application::$onStartup.
     *
     * The event listener method receives a @see Symplify\SymfonyEventDispatcher\Event\ApplicationEvent instance
     *
     * @var string
     */
    const ON_STARTUP = Application::class . '::onStartup';

    /**
     * The ON_SHUTDOWN event occurs before the application shuts down,.
     *
     * @see \Nette\Application\Application::$onShutdown.
     *
     * The event listener method receives a @see Symplify\SymfonyEventDispatcher\Event\ApplicationExceptionEvent instance
     *
     * @var string
     */
    const ON_SHUTDOWN = Application::class . '::onShutdown';

    /**
     * The ON_REQUEST event occurs when a new request is received,.
     *
     * @see \Nette\Application\Application::$onRequest.
     *
     * The event listener method receives a @see \Symplify\SymfonyEventDispatcher\Event\ApplicationRequestEvent instance
     *
     * @var string
     */
    const ON_REQUEST = Application::class . '::onRequest';

    /**
     * The ON_PRESENTER event when a presenter is created,.
     *
     * @see \Nette\Application\Application::$onPresenter.
     *
     * The event listener method receives a @see \Symplify\SymfonyEventDispatcher\Event\ApplicationPresenterEvent instance
     *
     * @var string
     */
    const ON_PRESENTER = Application::class . '::onPresenter';

    /**
     * The ON_RESPONSE event occurs when a new response is ready for dispatch,.
     *
     * @see \Nette\Application\Application::$onResponse.
     *
     * The event listener method receives a @see \Symplify\SymfonyEventDispatcher\Event\ApplicationResponseEvent instance
     *
     * @var string
     */
    const ON_RESPONSE = Application::class . '::onResponse';

    /**
     * The ON_ERROR event when an unhandled exception occurs in the application,.
     *
     * @see \Nette\Application\Application::$onError.
     *
     * The event listener method receives a @see \Symplify\SymfonyEventDispatcher\Event\ApplicationExceptionEvent instance
     *
     * @var string
     */
    const ON_ERROR = Application::class . '::onError';
}
