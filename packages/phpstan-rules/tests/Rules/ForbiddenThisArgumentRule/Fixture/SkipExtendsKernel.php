<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\Fixture;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

final class SkipExtendsKernel extends Kernel
{
    public function run()
    {
        $this->service->execute($this);
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
    }

    public function registerBundles(): iterable
    {
    }
}
