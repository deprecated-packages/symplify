<?php

declare(strict_types=1);

namespace Symplify\ParameterNameGuard\Tests\Guard;

use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\ParameterNameGuard\Exception\ParameterTypoException;
use Symplify\ParameterNameGuard\Tests\HttpKernel\ParameterNameGuardHttpKernel;

final class ParameterNameGuardTest extends AbstractKernelTestCase
{
    public function test(): void
    {
        $this->expectException(ParameterTypoException::class);
        $this->expectExceptionMessage('Parameter "parameters > typo" does not exist.
Use "parameters > correct" instead.');

        $this->bootKernelWithConfigs(ParameterNameGuardHttpKernel::class, [__DIR__ . '/config.yml']);
    }
}
