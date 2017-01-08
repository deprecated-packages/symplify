<?php declare(strict_types=1); 

namespace Symplify\SymfonySecurity\Tests\Adapter\Nette\DI\SymfonySecurityExtension\FirewallSource;

use Nette\Application\AbortException;
use Nette\Application\Application;
use Nette\Http\IRequest;
use Nette\Security\User;
use Symplify\SymfonySecurity\Contract\Http\FirewallHandlerInterface;

final class FirewallHandler implements FirewallHandlerInterface
{
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getFirewallName() : string
    {
        return 'adminFirewall';
    }

    public function handle(Application $application, IRequest $request)
    {
        if (! $this->user->isLoggedIn()) {
            throw new AbortException();
        }

        if (! $this->user->isInRole('admin')) {
            throw new AbortException();
        }
    }
}
