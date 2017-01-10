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


    protected function setUp()
    {
        parent::setUp();

        $this->configuration = $this->container->getByType(Configuration::class);
        $this->configuration->setMigrationsDirectory($this->getMigrationsDirectory());
    }


    public function testStatusCommand()
    {
        $input = new ArrayInput(['command' => 'migrations:status']);
        $output = new BufferedOutput;

        $result = $this->application->run($input, $output);
        $this->assertSame(0, $result);
    }


    public function testAvailableMigrations()
    {
        $this->assertSame(2, $this->configuration->getNumberOfAvailableMigrations());
    }


    /**
     * @return string
     */
    private function getMigrationsDirectory()
    {
        return __DIR__ . '/../Migrations';
    }
}
