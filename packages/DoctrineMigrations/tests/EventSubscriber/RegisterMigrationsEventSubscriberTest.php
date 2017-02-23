<?php declare(strict_types=1);

namespace Zenify\DoctrineMigrations\Tests\EventSubscriber;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

final class RegisterMigrationsEventSubscriberTest extends AbstractEventSubscriberTest
{
    /**
     * @var Configuration
     */
    private $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = $this->container->getByType(Configuration::class);
        $this->configuration->setMigrationsDirectory($this->getMigrationsDirectory());
    }

    public function testStatusCommand(): void
    {
        $input = new ArrayInput(['command' => 'migrations:status']);
        $output = new BufferedOutput;

        $result = $this->application->run($input, $output);
        $this->assertSame(0, $result);
    }

    public function testAvailableMigrations(): void
    {
        $this->assertSame(2, $this->configuration->getNumberOfAvailableMigrations());
    }

    private function getMigrationsDirectory(): string
    {
        return __DIR__ . '/../Migrations';
    }
}
