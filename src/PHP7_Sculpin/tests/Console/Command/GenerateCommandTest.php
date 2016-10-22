<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\Console\Command;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;
use Symplify\PHP7_Sculpin\Console\Application;
use Symplify\PHP7_Sculpin\DI\Container\ContainerFactory;

final class GenerateCommandTest extends TestCase
{
    public function test()
    {
        $application = $this->getApplicationForConfig(__DIR__.'/GenerateCommandSource/config/config.neon');

        $input = new ArgvInput(['Application name', 'generate']);

        $result = $application->run($input, new NullOutput());
        $this->assertSame(0, $result);

        $this->assertFileExists(__DIR__.'/GenerateCommandSource/output/index.html');
    }

    public function testException()
    {
        $application = $this->getApplicationForConfig(
            __DIR__.'/GenerateCommandSource/config/configWithMissingSource.neon'
        );

        $input = new ArgvInput(['Application name', 'generate']);

        $this->assertSame(1, $application->run($input, new NullOutput()));
    }

    protected function tearDown()
    {
        FileSystem::delete(__DIR__.'/GenerateCommandSource/output');
    }

    protected function getApplicationForConfig(string $config) : Application
    {
        $container = (new ContainerFactory())->createWithConfig($config);

        /* @var Application $application */
        $application = $container->getByType(Application::class);
        $application->setAutoExit(false);

        return $application;
    }
}
