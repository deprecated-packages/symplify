<?php

declare(strict_types=1);

namespace Symplify\AutoServiceRegistration\Tests\Adapter\Symfony\DependencyInjection\Extension;

use PHPUnit\Framework\TestCase;
use Symplify\AutoServiceRegistration\Adapter\Symfony\DependencyInjection\Extension\ContainerExtension;
use Symplify\AutoServiceRegistration\Adapter\Symfony\SymplifyAutoServiceRegistrationBundle;

final class ContainerExtensionTest extends TestCase
{
    public function test()
    {
        $containerExtension = new ContainerExtension();
        $this->assertSame(SymplifyAutoServiceRegistrationBundle::ALIAS, $containerExtension->getAlias());
    }
}
