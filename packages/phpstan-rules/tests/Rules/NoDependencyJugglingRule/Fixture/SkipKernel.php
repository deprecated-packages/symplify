<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDependencyJugglingRule\Fixture;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PHPStanRules\Tests\Rules\NoDependencyJugglingRule\Source\JuggleBall;

final class SkipKernel extends Kernel
{
    /**
     * @var JuggleBall
     */
    private $juggleBall;

    public function __construct()
    {
        $this->juggleBall = new JuggleBall();
    }

    public function run($value)
    {
        $value->call($this->juggleBall);
    }

    public function registerBundles()
    {
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }
}
