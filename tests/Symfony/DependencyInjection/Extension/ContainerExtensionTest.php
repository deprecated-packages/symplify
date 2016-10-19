<?php

declare(strict_types=1);

namespace Symplify\AutoServiceRegistration\Tests\Symfony\DependencyInjection\Extension;

use PHPUnit\Framework\TestCase;
use Symplify\AutoServiceRegistration\Symfony\DependencyInjection\Extension\ContainerExtension;
use Symplify\AutoServiceRegistration\Symfony\SymplifyAutoServiceRegistrationBundle;

final class ContainerExtensionTest extends TestCase
{
    public function test()
    {
        $containerExtension = new ContainerExtension();
        $this->assertSame(SymplifyAutoServiceRegistrationBundle::ALIAS, $containerExtension->getAlias());
    }
}
