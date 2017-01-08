<?php declare(strict_types=1);

namespace Symplify\SymfonySecurity\Contract\Http;

use Symplify\SymfonySecurity\Contract\HttpFoundation\RequestMatcherInterface;

interface FirewallMapFactoryInterface
{
    public function addRequestMatcher(RequestMatcherInterface $requestMatcher);

    public function addFirewallHandler(FirewallHandlerInterface $firewallHandler);

    public function create() : FirewallMapInterface;
}
