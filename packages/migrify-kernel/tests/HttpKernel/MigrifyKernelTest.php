<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\Tests\HttpKernel;

use Symfony\Component\Console\Application;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SymplifyKernel\Tests\HttpKernel\Source\DummySymplifyKernel;

final class MigrifyKernelTest extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernel(DummySymplifyKernel::class);
    }

    public function test(): void
    {
        $consoleApplication = self::$container->get(Application::class);
        $this->assertInstanceOf(Application::class, $consoleApplication);
    }
}
