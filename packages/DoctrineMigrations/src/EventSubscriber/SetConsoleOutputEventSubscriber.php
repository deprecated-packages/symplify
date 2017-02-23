<?php declare(strict_types=1);

namespace Zenify\DoctrineMigrations\EventSubscriber;

use Doctrine\DBAL\Migrations\Tools\Console\Command\AbstractCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zenify\DoctrineMigrations\OutputWriter;

final class SetConsoleOutputEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var OutputWriter
     */
    private $outputWriter;

    public function __construct(OutputWriter $outputWriter)
    {
        $this->outputWriter = $outputWriter;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [ConsoleEvents::COMMAND => 'setOutputWriter'];
    }

    public function setOutputWriter(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();
        if (! $this->isMigrationCommand($command)) {
            return;
        }

        $this->outputWriter->setConsoleOutput($event->getOutput());
    }

    private function isMigrationCommand(Command $command): bool
    {
        return $command instanceof AbstractCommand;
    }
}
