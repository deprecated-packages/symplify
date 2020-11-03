<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Fixture;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

final class SkipParentWithArgs extends Kernel
{
    public function __construct()
    {
        parent::__construct('protect', false);
    }

    public function registerBundles()
    {
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {

    }
}
