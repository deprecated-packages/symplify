<?php declare(strict_types=1);

namespace Zenify\DoctrineMigrations\Tests\EventSubscriber;

use PHPUnit\Framework\Assert;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Zenify\DoctrineMigrations\OutputWriter;

final class SetConsoleOutputEventSubscriberTest extends AbstractEventSubscriberTest
{
    /**
     * @var OutputWriter
     */
    private $outputWriter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->outputWriter = $this->container->getByType(OutputWriter::class);
    }

    public function testDispatching(): void
    {
        $this->assertNull(
            Assert::getObjectAttribute($this->outputWriter, 'consoleOutput')
        );

        $input = new ArrayInput(['command' => 'migrations:status']);
        $output = new BufferedOutput;
        $this->application->run($input, $output);

        $this->assertInstanceOf(
            OutputInterface::class,
            Assert::getObjectAttribute($this->outputWriter, 'consoleOutput')
        );
    }
}
