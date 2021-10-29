<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\DependencyInjection\CompilerPass;

use Symfony\Component\Console\Application;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\PackageBuilder\Tests\DependencyInjection\CompilerPass\Source\SomeCommand;
use Symplify\PackageBuilder\Tests\HttpKernel\PackageBuilderKernel;

final class NamelessConsoleCommandCompilerPassTest extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(PackageBuilderKernel::class, [__DIR__ . '/config/command_config.php']);
    }

    public function test(): void
    {
        /** @var Application $application */
        $application = $this->getService(Application::class);
        $this->assertInstanceOf(Application::class, $application);

        $someCommand = $application->get('some');
        $this->assertInstanceOf(SomeCommand::class, $someCommand);
    }
}
