<?php

declare(strict_types=1);

/*
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace Symplify\SymfonySecurity\EventSubscriber;

use Nette\Application\UI\ComponentReflection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symplify\SymfonyEventDispatcher\Event\ApplicationPresenterEvent;
use Symplify\SymfonyEventDispatcher\NetteApplicationEvents;

final class CheckRequirementsSubscriber implements EventSubscriberInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public static function getSubscribedEvents() : array
    {
        return [
            NetteApplicationEvents::ON_PRESENTER => 'onPresenter',
        ];
    }

    public function onPresenter(ApplicationPresenterEvent $applicationPresenterEvent)
    {
        $this->authorizationChecker->isGranted(
            'access',
            new ComponentReflection($applicationPresenterEvent->getPresenter())
        );
    }
}
