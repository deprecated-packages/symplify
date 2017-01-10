<?php declare(strict_types=1);

namespace Zenify\DoctrineMigrations\Tests\Configuration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Version;
use PHPUnit\Framework\TestCase;
use Zenify\DoctrineMigrations\Tests\Configuration\ConfigurationSource\SomeService;
use Zenify\DoctrineMigrations\Tests\ContainerFactory;
use Zenify\DoctrineMigrations\Tests\Migrations\Version123;

final class ConfigurationTest extends TestCase
{

    /**
     * @var Configuration
     */
    private $configuration;


    protected function setUp()
    {
        $container = (new ContainerFactory)->create();
        $this->configuration = $container->getByType(Configuration::class);

        $this->configuration->registerMigrationsFromDirectory(
            $this->configuration->getMigrationsDirectory()
        );
    }


    public function testInject()
    {
        $migrations = $this->configuration->getMigrationsToExecute('up', 123);
        $this->assertCount(1, $migrations);

        /** @var Version $version */
        $version = $migrations[123];
        $this->assertInstanceOf(Version::class, $version);

        /** @var AbstractMigration|Version123 $migration */
        $migration = $version->getMigration();
        $this->assertInstanceOf(AbstractMigration::class, $migration);

        $this->assertInstanceOf(SomeService::class, $migration->someService);
    }


    public function testLoadMigrationsFromSubdirs()
    {
        $migrations = $this->configuration->getMigrations();
        $this->assertCount(2, $migrations);
    }
}
