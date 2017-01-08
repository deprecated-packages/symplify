<?php declare(strict_types=1);

namespace Symplify\SymfonySecurity\Contract\HttpFoundation;

use Nette\Http\IRequest;
use Symplify\SymfonySecurity\Contract\DI\ModularFirewallInterface;

/**
 * Mimics @see \Symfony\Component\HttpFoundation\RequestMatcherInterface.
 */
interface RequestMatcherInterface extends ModularFirewallInterface
{
    /**
     * Decides whether the rule(s) implemented by the strategy matches the supplied request.
     */
    public function matches(IRequest $request) : bool;
}
