<?php declare(strict_types=1);

namespace Symplify\DoctrineMigrations\DI;

use Doctrine\DBAL\Migrations\Tools\Console\Command\AbstractCommand;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symfony\Component\Console\Application;
use Symplify\DoctrineMigrations\Configuration\Configuration;
use Symplify\DoctrineMigrations\EventSubscriber\RegisterMigrationsEventSubscriber;
use Symplify\DoctrineMigrations\EventSubscriber\SetConsoleOutputEventSubscriber;
use Symplify\DoctrineMigrations\Exception\DI\MissingExtensionException;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\DI\SymfonyEventDispatcherExtension;

final class MigrationsExtension extends CompilerExtension
{
    /**
     * @var string[]
     */
    private $defaults = [
        'table' => 'doctrine_migrations',
        'column' => 'version',
        'directory' => '%appDir%/../migrations',
        'namespace' => 'Migrations',
        'versionsOrganization' => null,
    ];

    /**
     * @var string[]
     */
    private $subscribers = [
        RegisterMigrationsEventSubscriber::class,
        SetConsoleOutputEventSubscriber::class,
    ];

    public function loadConfiguration(): void
    {
        $this->ensureEventDispatcherExtensionIsRegistered();

        $containerBuilder = $this->getContainerBuilder();

        Compiler::loadDefinitions(
            $containerBuilder,
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );

        foreach ($this->subscribers as $key => $subscriber) {
            $containerBuilder->addDefinition($this->prefix('listener' . $key))
                ->setClass($subscriber);
        }

        $config = $this->getValidatedConfig();

        $this->addConfigurationDefinition($config);
    }

    public function beforeCompile(): void
    {
        $containerBuilder = $this->getContainerBuilder();
        $containerBuilder->prepareClassList();

        $this->setConfigurationToCommands();
        $this->loadCommandsToApplication();
    }

    /**
     * @param mixed[] $config
     */
    private function addConfigurationDefinition(array $config): void
    {
        $containerBuilder = $this->getContainerBuilder();
        $configurationDefinition = $containerBuilder->addDefinition($this->prefix('configuration'));
        $configurationDefinition
            ->setClass(Configuration::class)
            ->addSetup('setMigrationsTableName', [$config['table']])
            ->addSetup('setMigrationsColumnName', [$config['column']])
            ->addSetup('setMigrationsDirectory', [$config['directory']])
            ->addSetup('setMigrationsNamespace', [$config['namespace']]);

        if ($config['versionsOrganization'] === Configuration::VERSIONS_ORGANIZATION_BY_YEAR) {
            $configurationDefinition->addSetup('setMigrationsAreOrganizedByYear');
        } elseif ($config['versionsOrganization'] === Configuration::VERSIONS_ORGANIZATION_BY_YEAR_AND_MONTH) {
            $configurationDefinition->addSetup('setMigrationsAreOrganizedByYearAndMonth');
        }
    }

    private function setConfigurationToCommands(): void
    {
        $containerBuilder = $this->getContainerBuilder();
        $configurationDefinition = $containerBuilder->getDefinitionByType(Configuration::class);

        foreach ($containerBuilder->findByType(AbstractCommand::class) as $commandDefinition) {
            $commandDefinition->addSetup('setMigrationConfiguration', ['@' . $configurationDefinition->getClass()]);
        }
    }

    private function loadCommandsToApplication(): void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            Application::class,
            AbstractCommand::class,
            'add'
        );
    }

    /**
     * @return mixed[]
     */
    private function getValidatedConfig(): array
    {
        $configuration = $this->validateConfig($this->defaults);
        $this->validateConfig($configuration);
        $configuration['directory'] = $this->getContainerBuilder()->expand($configuration['directory']);

        return $configuration;
    }

    private function ensureEventDispatcherExtensionIsRegistered(): void
    {
        if (! $this->compiler->getExtensions(SymfonyEventDispatcherExtension::class)) {
            throw new MissingExtensionException(
                sprintf('Please register required extension "%s" to your config.', SymfonyEventDispatcherExtension::class)
            );
        }
    }
}
