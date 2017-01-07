<?php

declare(strict_types=1);

namespace Symplify\SymfonySecurity\Contract\Http;

use Nette\Application\Application;
use Nette\Http\IRequest;
use Symplify\SymfonySecurity\Contract\DI\ModularFirewallInterface;

/**
 * Mimics @see \Symfony\Component\Security\Http\Firewall\ListenerInterface.
 */
interface FirewallHandlerInterface extends ModularFirewallInterface
{
    public function handle(Application $application, IRequest $request);
}
