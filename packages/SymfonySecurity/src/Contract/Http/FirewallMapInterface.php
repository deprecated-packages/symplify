<?php

declare(strict_types=1);

namespace Symplify\SymfonySecurity\Contract\Http;

use Nette\Http\IRequest;
use Symfony\Component\Security\Http\Firewall\ExceptionListener;
use Symplify\SymfonySecurity\Contract\HttpFoundation\RequestMatcherInterface;

/**
 * Mimics @see \Symfony\Component\Security\Http\FirewallMapInterface.
 */
interface FirewallMapInterface
{
    public function add(
        ?RequestMatcherInterface $requestMatcher,
        array $listeners = [],
        ExceptionListener $exceptionListener = null
    );

    /**
     * Returns the authentication listeners.
     *
     * If there are no authentication listeners, the first inner array must be
     * empty.
     *
     * If there is no exception listener, the second element of the outer array
     * must be null.
     *
     * @return array of the format array(array(AuthenticationListener), ExceptionListener)
     */
    public function getListeners(IRequest $request) : array;
}
