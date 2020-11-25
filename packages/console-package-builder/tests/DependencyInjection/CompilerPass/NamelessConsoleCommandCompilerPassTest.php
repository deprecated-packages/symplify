<?php

declare(strict_types=1);

namespace Symplify\ConsolePackageBuilder\Tests\DependencyInjection\CompilerPass;

use Symfony\Component\Console\Application;
use Symplify\ConsolePackageBuilder\Tests\DependencyInjection\CompilerPass\Source\SomeCommand;
use Symplify\ConsolePackageBuilder\Tests\HttpKernel\ConsolePackageBuilderKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class NamelessConsoleCommandCompilerPassTest extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(ConsolePackageBuilderKernel::class, [__DIR__ . '/config/command_config.php']);
    }

    public function test(): void
    {
        /** @var Application $application */
        $application = self::$container->get(Application::class);
        $this->assertInstanceOf(Application::class, $application);

        $someCommand = $application->get('some');
        $this->assertInstanceOf(SomeCommand::class, $someCommand);
    }
}
