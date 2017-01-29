<?php declare(strict_types=1);

namespace Symplify\SymfonySecurityVoters\EventSubscriber;

use Nette\Application\UI\ComponentReflection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterCreatedEvent;

final class CheckRequirementsSubscriber implements EventSubscriberInterface
{
    /**
     * @var AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager)
    {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    public static function getSubscribedEvents() : array
    {
        return [
            PresenterCreatedEvent::NAME => 'onPresenter',
        ];
    }

    public function onPresenter(PresenterCreatedEvent $applicationPresenterEvent) : void
    {
//        $this->accessDecisionManager->decide()
        $this->authorizationChecker->isGranted(
            'access',
            new ComponentReflection($applicationPresenterEvent->getPresenter())
        );
    }
}
