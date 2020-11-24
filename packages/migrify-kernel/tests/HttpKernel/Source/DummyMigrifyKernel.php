<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\Tests\HttpKernel\Source;

use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

final class DummySymplifyKernel extends AbstractSymplifyKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
    }
}
