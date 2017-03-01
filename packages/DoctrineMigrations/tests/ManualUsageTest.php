<?php declare(strict_types=1);

namespace Symplify\DoctrineMigrations\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

final class ManualUsageTest extends TestCase
{
    /**
     * @var Application
     */
    private $consoleApplication;

    protected function setUp(): void
    {
        $container = (new ContainerFactory)->createWithConfig(__DIR__ . '/config/manualUsage.neon');
        $this->consoleApplication = $container->getByType(Application::class);
    }

    public function testStatus(): void
    {
        $input = new ArrayInput(['command' => 'migrations:status']);
        $output = new BufferedOutput;

        $status = $this->consoleApplication->run($input, $output);

        $this->assertSame(0, $status);
        $this->assertContains('Configuration', $output->fetch());
    }

    public function testMigrate(): void
    {
        $input = new ArrayInput(['command' => 'migrations:migrate']);
        $input->setInteractive(false);

        $output = new BufferedOutput;

        $status = $this->consoleApplication->run($input, $output);
        $this->assertSame(0, $status);
    }
}
