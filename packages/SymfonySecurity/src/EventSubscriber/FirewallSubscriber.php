<?php

declare(strict_types=1);

/*
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace Symplify\SymfonySecurity\EventSubscriber;

use Nette\Http\IRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\ApplicationPresenterEvent;
use Symplify\SymfonySecurity\Contract\Http\FirewallHandlerInterface;
use Symplify\SymfonySecurity\Contract\Http\FirewallMapInterface;

/**
 * Mimics @see \Symfony\Component\Security\Http\Firewall.
 */
final class FirewallSubscriber implements EventSubscriberInterface
{
    /**
     * @var FirewallMapInterface
     */
    private $firewallMap;

    /**
     * @var IRequest
     */
    private $request;

    public function __construct(FirewallMapInterface $firewallMap, IRequest $request)
    {
        $this->firewallMap = $firewallMap;
        $this->request = $request;
    }

    public static function getSubscribedEvents() : array
    {
        return [
            ApplicationPresenterEvent::ON_PRESENTER => 'onPresenter',
        ];
    }

    public function onPresenter(ApplicationPresenterEvent $applicationPresenterEvent)
    {
        /** @var FirewallHandlerInterface[] $listeners */
        list($listeners) = $this->firewallMap->getListeners($this->request);
        foreach ($listeners as $listener) {
            $listener->handle($applicationPresenterEvent->getApplication(), $this->request);
        }
    }
}
