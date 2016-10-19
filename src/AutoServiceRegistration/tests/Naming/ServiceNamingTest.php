<?php

declare(strict_types=1);

namespace Symplify\AutoServiceRegistration\Naming;

use PHPUnit\Framework\TestCase;

final class ServiceNamingTest extends TestCase
{
    public function test()
    {
        $this->assertSame(
            'symplify.autoserviceregistration.naming.servicenamingtest',
            ServiceNaming::createServiceIdFromClass(get_class())
        );
    }
}
