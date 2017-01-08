<?php declare(strict_types=1);

namespace Symplify\SymfonySecurity\Http;

use Symplify\SymfonySecurity\Contract\Http\FirewallHandlerInterface;
use Symplify\SymfonySecurity\Contract\Http\FirewallMapFactoryInterface;
use Symplify\SymfonySecurity\Contract\Http\FirewallMapInterface;
use Symplify\SymfonySecurity\Contract\HttpFoundation\RequestMatcherInterface;

final class FirewallMapFactory implements FirewallMapFactoryInterface
{
    /**
     * @var RequestMatcherInterface[]
     */
    private $requestMatchers = [];

    /**
     * @var FirewallHandlerInterface[][]
     */
    private $firewallHandlers = [];

    public function addRequestMatcher(RequestMatcherInterface $requestMatcher)
    {
        $this->requestMatchers[$requestMatcher->getFirewallName()] = $requestMatcher;
    }

    public function addFirewallHandler(FirewallHandlerInterface $firewallHandler)
    {
        $this->firewallHandlers[$firewallHandler->getFirewallName()][] = $firewallHandler;
    }

    public function create() : FirewallMapInterface
    {
        $firewallMap = new FirewallMap();
        foreach ($this->requestMatchers as $firewallName => $requestMatcher) {
            if (isset($this->firewallHandlers[$firewallName])) {
                $firewallMap->add($requestMatcher, $this->firewallHandlers[$firewallName]);
            }
        }

        return $firewallMap;
    }
}
