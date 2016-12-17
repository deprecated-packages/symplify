<?php

declare(strict_types=1);

/*
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz)
 */

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
