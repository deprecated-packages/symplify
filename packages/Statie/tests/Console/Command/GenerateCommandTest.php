<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Console\Command;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symplify\Statie\Console\ConsoleApplication;
use Symplify\Statie\DI\Container\ContainerFactory;

final class GenerateCommandTest extends TestCase
{
    /**
     * @var ConsoleApplication
     */
    private $application;

    protected function setUp()
    {
        $container = (new ContainerFactory)->create();

        $this->application = $container->getByType(ConsoleApplication::class);
        $this->application->setAutoExit(false);
    }

    public function test()
    {
        $stringInput = sprintf(
            'generate --source %s --output %s',
            __DIR__ . '/GenerateCommandSource/source',
            __DIR__ . '/GenerateCommandSource/output'
        );

        $input = new StringInput($stringInput);
        $result = $this->application->run($input, new NullOutput);
        $this->assertSame(0, $result);

        $this->assertFileExists(__DIR__ . '/GenerateCommandSource/output/index.html');
    }

    public function testException()
    {
        $stringInput = sprintf(
            'generate --source %s',
            __DIR__ . DIRECTORY_SEPARATOR . 'GenerateCommandSource' . 'missing'
        );
        $input = new StringInput($stringInput);

        $this->assertSame(1, $this->application->run($input, new NullOutput));
    }

    protected function tearDown()
    {
        FileSystem::delete(__DIR__ . DIRECTORY_SEPARATOR . 'GenerateCommandSource' . DIRECTORY_SEPARATOR . 'output');
    }
}
