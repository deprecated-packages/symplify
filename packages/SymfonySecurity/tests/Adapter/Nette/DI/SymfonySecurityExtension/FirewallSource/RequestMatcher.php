<?php declare(strict_types=1); 

namespace Symplify\SymfonySecurity\Tests\Adapter\Nette\DI\SymfonySecurityExtension\FirewallSource;

use Nette\Http\IRequest;
use Symplify\SymfonySecurity\Contract\HttpFoundation\RequestMatcherInterface;

final class RequestMatcher implements RequestMatcherInterface
{
    public function getFirewallName() : string
    {
        return 'adminFirewall';
    }

    public function matches(IRequest $request) : bool
    {
        $url = $request->getUrl();
        // match all, just for testing purposes only
        return strpos($url->getScriptPath(), '/') === 0;
    }
}
