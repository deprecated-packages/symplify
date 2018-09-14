<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\EventSubscriber;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\MonorepoBuilder\Console\Command\BumpInterdependencyCommand;
use Symplify\MonorepoBuilder\Console\Command\MergeCommand;
use Symplify\MonorepoBuilder\Console\Command\ReleaseCommand;
use Symplify\MonorepoBuilder\Console\Command\ValidateCommand;
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
        if ($consoleEvent->getCommand() === null) {
            return;
        }

        if (in_array($consoleEvent->getCommand(), [ValidateCommand::class, MergeCommand::class], true)) {
            $this->sourcesPresenceValidator->validatePackageComposerJsons();
        }

        if (in_array($consoleEvent->getCommand(), [BumpInterdependencyCommand::class, ReleaseCommand::class], true)) {
            $this->sourcesPresenceValidator->validateRootComposerJsonName();
        }
    }
}
