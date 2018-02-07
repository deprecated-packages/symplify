<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Console\Command;

use Nette\Utils\FileSystem;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symplify\Statie\Console\Application;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class GenerateCommandTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private $outputDirectory = __DIR__ . '/GenerateCommandSource/output';

    /**
     * @var Application
     */
    private $application;

    protected function setUp(): void
    {
        $this->application = $this->container->get(Application::class);
        $this->application->setAutoExit(false);
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->outputDirectory);
    }

    public function test(): void
    {
        $stringInput = ['generate', __DIR__ . '/GenerateCommandSource/source', '--output', $this->outputDirectory];
        $input = new StringInput(implode(' ', $stringInput));

        $result = $this->application->run($input, new NullOutput());
        $this->assertSame(0, $result);

        $this->assertFileExists($this->outputDirectory . '/index.html');
    }

    public function testException(): void
    {
        $stringInput = sprintf('generate --source %s', __DIR__ . '/GenerateCommandSource/missing');
        $input = new StringInput($stringInput);

        $this->assertSame(1, $this->application->run($input, new NullOutput()));
    }
}
