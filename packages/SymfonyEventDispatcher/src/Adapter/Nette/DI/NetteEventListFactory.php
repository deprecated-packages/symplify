<?php

declare(strict_types=1);

/**
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\DI;

use Nette\Application\Application;
use Nette\Application\UI\Presenter;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationExceptionEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationPresenterEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationRequestEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationResponseEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterResponseEvent;

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
            ApplicationRequestEvent::ON_REQUEST
        );
        $list[] = new NetteEventItem(
            Application::class,
            'onStartup',
            ApplicationEvent::class,
            ApplicationEvent::ON_STARTUP
        );
        $list[] = new NetteEventItem(
            Application::class,
            'onPresenter',
            ApplicationPresenterEvent::class,
            ApplicationPresenterEvent::ON_PRESENTER
        );
        $list[] = new NetteEventItem(
            Application::class,
            'onResponse',
            ApplicationResponseEvent::class,
            ApplicationResponseEvent::ON_RESPONSE
        );
        $list[] = new NetteEventItem(
            Application::class,
            'onError',
            ApplicationExceptionEvent::class,
            ApplicationExceptionEvent::ON_ERROR
        );
        $list[] = new NetteEventItem(
            Application::class,
            'onShutdown',
            ApplicationExceptionEvent::class,
            ApplicationExceptionEvent::ON_SHUTDOWN
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
            PresenterResponseEvent::ON_SHUTDOWN
        );

        return $list;
    }
}
