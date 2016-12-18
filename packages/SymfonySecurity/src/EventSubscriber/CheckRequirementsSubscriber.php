<?php

declare(strict_types=1);

/*
 * This file is part of Symplify.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace Symplify\SymfonySecurity\EventSubscriber;

use Nette\Application\UI\ComponentReflection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterCreatedEvent;

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
            PresenterCreatedEvent::NAME => 'onPresenter',
        ];
    }

    public function onPresenter(PresenterCreatedEvent $applicationPresenterEvent)
    {
        $this->authorizationChecker->isGranted(
            'access',
            new ComponentReflection($applicationPresenterEvent->getPresenter())
        );
    }
}
