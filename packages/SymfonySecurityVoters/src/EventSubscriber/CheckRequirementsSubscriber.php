<?php declare(strict_types=1);

namespace Symplify\SymfonySecurityVoters\EventSubscriber;

use Nette\Application\AbortException;
use Nette\Application\UI\ComponentReflection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\Event\PresenterCreatedEvent;

final class CheckRequirementsSubscriber implements EventSubscriberInterface
{
    /**
     * @var AccessDecisionManager
     */
    private $accessDecisionManager;

    /**
     * @var TokenInterface
     */
    private $token;

    public function __construct(AccessDecisionManager $accessDecisionManager, TokenInterface $token)
    {
        $this->accessDecisionManager = $accessDecisionManager;
        $this->token = $token;
    }

    public static function getSubscribedEvents() : array
    {
        return [
            PresenterCreatedEvent::NAME => 'onPresenter',
        ];
    }

    public function onPresenter(PresenterCreatedEvent $applicationPresenterEvent) : void
    {
        $presenterReflection = new ComponentReflection($applicationPresenterEvent->getPresenter());
        $hasAccess = $this->accessDecisionManager->decide($this->token, ['access'], $presenterReflection);
        if ($hasAccess === FALSE) {
            throw new AbortException;
        }
    }
}
