<?php declare(strict_types=1);

namespace Zenify\DoctrineMigrations\EventSubscriber;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Tools\Console\Command\AbstractCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RegisterMigrationsEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public static function getSubscribedEvents(): array
    {
        return [ConsoleEvents::COMMAND => 'registerMigrations'];
    }

    public function registerMigrations(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();
        if (! $this->isMigrationCommand($command)) {
            return;
        }

        $this->configuration->registerMigrationsFromDirectory(
            $this->configuration->getMigrationsDirectory()
        );
    }

    private function isMigrationCommand(Command $command): bool
    {
        return $command instanceof AbstractCommand;
    }
}
