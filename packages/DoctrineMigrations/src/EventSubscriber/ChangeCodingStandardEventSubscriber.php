<?php

declare(strict_types=1);

namespace Zenify\DoctrineMigrations\EventSubscriber;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Tools\Console\Helper\MigrationDirectoryHelper;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zenify\DoctrineMigrations\Contract\CodeStyle\CodeStyleInterface;

final class ChangeCodingStandardEventSubscriber implements EventSubscriberInterface
{

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var CodeStyleInterface
     */
    private $codeStyle;


    public function __construct(Configuration $configuration, CodeStyleInterface $codeStyle)
    {
        $this->codeStyle = $codeStyle;
        $this->configuration = $configuration;
    }


    public static function getSubscribedEvents() : array
    {
        return [ConsoleEvents::TERMINATE => 'applyCodingStyle'];
    }


    public function applyCodingStyle(ConsoleTerminateEvent $event)
    {
        $command = $event->getCommand();
        if (! $this->isAllowedCommand($command->getName())) {
            return;
        }

        $filename = $this->getCurrentMigrationFileName();
        if (file_exists($filename)) {
            $this->codeStyle->applyForFile($filename);
        }
    }


    private function isAllowedCommand(string $name) : bool
    {
        return in_array($name, ['migrations:generate', 'migrations:diff']);
    }


    private function getCurrentMigrationFileName() : string
    {
        $version = $this->getCurrentVersionName();

        $i = 0;
        while (! file_exists($this->getMigrationFileByVersion($version)) && $i <= 10) {
            $version--;
            $i++;
        }

        $path = $this->getMigrationFileByVersion($version);
        return $path;
    }


    private function getCurrentVersionName() : string
    {
        return date('YmdHis');
    }


    private function getMigrationFileByVersion(string $version) : string
    {
        $migrationDirectoryHelper = new MigrationDirectoryHelper($this->configuration);
        return $migrationDirectoryHelper->getMigrationDirectory() . '/Version' . $version . '.php';
    }
}
