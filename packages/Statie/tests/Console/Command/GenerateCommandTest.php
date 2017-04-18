<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Console\Command;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class GenerateCommandTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(
            __DIR__ . '/../../../src/config/config.neon'
        );

        $this->application = $container->getByType(Application::class);
        $this->application->setAutoExit(false);
    }

    protected function tearDown(): void
    {
        FileSystem::delete(__DIR__ . DIRECTORY_SEPARATOR . 'GenerateCommandSource' . DIRECTORY_SEPARATOR . 'output');
    }

    public function test(): void
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

    public function testException(): void
    {
        $stringInput = sprintf(
            'generate --source %s',
            __DIR__ . DIRECTORY_SEPARATOR . 'GenerateCommandSource' . 'missing'
        );
        $input = new StringInput($stringInput);

        $this->assertSame(1, $this->application->run($input, new NullOutput));
    }
}
