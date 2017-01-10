<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests\DependencyInjection\Extension;

use PHPUnit\Framework\TestCase;
use Symplify\ControllerAutowire\DependencyInjection\Extension\ContainerExtension;
use Symplify\ControllerAutowire\SymplifyControllerAutowireBundle;

final class ContainerExtensionTest extends TestCase
{
    public function testGetAlias()
    {
        $containerExtension = new ContainerExtension;
        $this->assertSame(SymplifyControllerAutowireBundle::ALIAS, $containerExtension->getAlias());
    }
}
