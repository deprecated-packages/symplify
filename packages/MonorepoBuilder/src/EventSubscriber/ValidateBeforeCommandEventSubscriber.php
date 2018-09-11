<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\EventSubscriber;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\MonorepoBuilder\Init\Command\InitCommand;
use Symplify\MonorepoBuilder\Validator\SourcesPresenceValidator;

final class ValidateBeforeCommandEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var SourcesPresenceValidator
     */
    private $sourcesPresenceValidator;

    public function __construct(SourcesPresenceValidator $sourcesPresenceValidator)
    {
        $this->sourcesPresenceValidator = $sourcesPresenceValidator;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [ConsoleEvents::COMMAND => 'validate'];
    }

    public function validate(ConsoleEvent $consoleEvent): void
    {
        if ($consoleEvent->getCommand() instanceof InitCommand) {
            return;
        }

        $this->sourcesPresenceValidator->validate();
    }
}
