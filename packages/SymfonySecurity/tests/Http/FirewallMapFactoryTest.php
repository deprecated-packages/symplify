<?php declare(strict_types=1);

namespace Symplify\SymfonySecurity\Tests\Http;

use PHPUnit\Framework\TestCase;
use Symplify\SymfonySecurity\Contract\Http\FirewallHandlerInterface;
use Symplify\SymfonySecurity\Contract\Http\FirewallMapFactoryInterface;
use Symplify\SymfonySecurity\Contract\Http\FirewallMapInterface;
use Symplify\SymfonySecurity\Contract\HttpFoundation\RequestMatcherInterface;
use Symplify\SymfonySecurity\Http\FirewallMapFactory;

final class FirewallMapFactoryTest extends TestCase
{
    public function testCreate()
    {
        $firewallMapFactory = $this->createLoadedFirewallMapFactory();
        $firewallMap = $firewallMapFactory->create();
        $this->assertInstanceOf(FirewallMapInterface::class, $firewallMap);
    }

    private function createLoadedFirewallMapFactory() : FirewallMapFactoryInterface
    {
        $firewallMapFactory = new FirewallMapFactory();

        $requestMatcherMock = $this->prophesize(RequestMatcherInterface::class);
        $requestMatcherMock->getFirewallName()->willReturn('someFirewall');
        $firewallMapFactory->addRequestMatcher($requestMatcherMock->reveal());

        $firewallHandlerMock = $this->prophesize(FirewallHandlerInterface::class);
        $firewallHandlerMock->getFirewallName()->willReturn('someFirewall');
        $firewallMapFactory->addFirewallHandler($firewallHandlerMock->reveal());

        return $firewallMapFactory;
    }
}
