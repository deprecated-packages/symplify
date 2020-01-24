<?php

declare(strict_types=1);

namespace Symplify\AutoBindParameter\Tests\DependencyInjection\CompilerPass;

use Symplify\AutoBindParameter\Tests\HttpKernel\AutoBindParameterHttpKernel;
use Symplify\AutoBindParameter\Tests\Source\SomeServiceWithParameter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class AutoBindParameterCompilerPassTest extends AbstractKernelTestCase
{
    public function test(): void
    {
        $this->bootKernel(AutoBindParameterHttpKernel::class);

        /** @var SomeServiceWithParameter $someServiceWithParameter */
        $someServiceWithParameter = self::$container->get(SomeServiceWithParameter::class);

        $this->assertSame('Johny', $someServiceWithParameter->getSuperName());
    }
}
