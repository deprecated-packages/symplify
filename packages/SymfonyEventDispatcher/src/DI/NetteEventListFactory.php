<?php

declare(strict_types=1);

/**
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\SymfonyEventDispatcher\DI;

use Nette\Application\Application;
use Nette\Application\UI\Presenter;
use Symplify\SymfonyEventDispatcher\Event\ApplicationEvent;
use Symplify\SymfonyEventDispatcher\Event\ApplicationExceptionEvent;
use Symplify\SymfonyEventDispatcher\Event\ApplicationPresenterEvent;
use Symplify\SymfonyEventDispatcher\Event\ApplicationRequestEvent;
use Symplify\SymfonyEventDispatcher\Event\ApplicationResponseEvent;
use Symplify\SymfonyEventDispatcher\Event\PresenterResponseEvent;
use Symplify\SymfonyEventDispatcher\NetteApplicationEvents;
use Symplify\SymfonyEventDispatcher\NettePresenterEvents;

final class NetteEventListFactory
{
    /**
     * @return NetteEventItem[]
     */
    public function create() : array
    {
        $list = [];
        $list = $this->addApplicationEventItems($list);
        $list = $this->addPresenterEventItems($list);

        return $list;
    }

    /**
     * @param NetteEventItem[] $list
     *
     * @return NetteEventItem[]
     */
    private function addApplicationEventItems(array $list) : array
    {
        $list[] = new NetteEventItem(
            Application::class,
            'onRequest',
            ApplicationRequestEvent::class,
            NetteApplicationEvents::ON_REQUEST
        );
        $list[] = new NetteEventItem(
            Application::class,
            'onStartup',
            ApplicationEvent::class,
            NetteApplicationEvents::ON_STARTUP
        );
        $list[] = new NetteEventItem(
            Application::class,
            'onPresenter',
            ApplicationPresenterEvent::class,
            NetteApplicationEvents::ON_PRESENTER
        );
        $list[] = new NetteEventItem(
            Application::class,
            'onResponse',
            ApplicationResponseEvent::class,
            NetteApplicationEvents::ON_RESPONSE
        );
        $list[] = new NetteEventItem(
            Application::class,
            'onError',
            ApplicationExceptionEvent::class,
            NetteApplicationEvents::ON_ERROR
        );
        $list[] = new NetteEventItem(
            Application::class,
            'onShutdown',
            ApplicationExceptionEvent::class,
            NetteApplicationEvents::ON_SHUTDOWN
        );

        return $list;
    }

    /**
     * @param NetteEventItem[] $list
     *
     * @return NetteEventItem[]
     */
    private function addPresenterEventItems(array $list) : array
    {
        $list[] = new NetteEventItem(
            Presenter::class,
            'onShutdown',
            PresenterResponseEvent::class,
            NettePresenterEvents::ON_SHUTDOWN
        );

        return $list;
    }
}
