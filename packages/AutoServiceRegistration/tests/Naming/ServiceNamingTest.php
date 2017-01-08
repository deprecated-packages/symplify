<?php declare(strict_types=1);

namespace Symplify\AutoServiceRegistration\Tests\Naming;

use PHPUnit\Framework\TestCase;
use Symplify\AutoServiceRegistration\Naming\ServiceNaming;

final class ServiceNamingTest extends TestCase
{
    public function test()
    {
        $this->assertSame(
            'symplify.autoserviceregistration.tests.naming.servicenamingtest',
            ServiceNaming::createServiceIdFromClass(get_class())
        );
    }
}
