<?php declare(strict_types=1);

namespace Symplify\SymfonySecurityVoters\EventSubscriber;

use Nette\Application\AbortException;
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

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PresenterCreatedEvent::NAME => 'onPresenter',
        ];
    }

    public function onPresenter(PresenterCreatedEvent $applicationPresenterEvent): void
    {
        $isGranted = $this->authorizationChecker->isGranted(
            'access',
            new ComponentReflection($applicationPresenterEvent->getPresenter())
        );

        if ($isGranted === false) {
            throw new AbortException;
        }
    }
}
