<?php

declare(strict_types=1);

/**
 * This file is part of Symplify.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\DI;

use Nette\Application\Application;
use Nette\Application\UI\Presenter;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationStartupEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationErrorEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterCreatedEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\RequestRecievedEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationResponseEvent;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterShutdownEvent;

final class NetteEventListFactory
{
    /**
     * @return NetteEventItem[]
     */
    public function create() : array
    {
        return array_merge(
            $this->createApplicationEventItems(),
            $this->createPresenterEventItems()
        );
    }

    /**
     * @return NetteEventItem[]
     */
    private function createApplicationEventItems() : array
    {
        $eventItems = [];
        $eventItems[] = new NetteEventItem(
            Application::class,
            'onRequest',
            RequestRecievedEvent::class,
            RequestRecievedEvent::NAME
        );
        $eventItems[] = new NetteEventItem(
            Application::class,
            'onStartup',
            ApplicationStartupEvent::class,
            ApplicationStartupEvent::NAME
        );
        $eventItems[] = new NetteEventItem(
            Application::class,
            'onPresenter',
            PresenterCreatedEvent::class,
            PresenterCreatedEvent::NAME
        );
        $eventItems[] = new NetteEventItem(
            Application::class,
            'onResponse',
            ApplicationResponseEvent::class,
            ApplicationResponseEvent::NAME
        );
        $eventItems[] = new NetteEventItem(
            Application::class,
            'onError',
            ApplicationErrorEvent::class,
            ApplicationErrorEvent::NAME
        );
        $eventItems[] = new NetteEventItem(
            Application::class,
            'onShutdown',
            ApplicationErrorEvent::class,
            ApplicationErrorEvent::NAME
        );

        return $eventItems;
    }

    /**
     * @return NetteEventItem[]
     */
    private function createPresenterEventItems() : array
    {
        return [new NetteEventItem(
            Presenter::class,
            'onShutdown',
            PresenterShutdownEvent::class,
            PresenterShutdownEvent::NAME
        )];
    }
}
