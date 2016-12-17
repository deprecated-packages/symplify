<?php

declare(strict_types=1);

/*
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace Symplify\SymfonySecurity\Http;

use Nette\Http\IRequest;
use Symfony\Component\Security\Http\Firewall\ExceptionListener;
use Symplify\SymfonySecurity\Contract\Http\FirewallMapInterface;
use Symplify\SymfonySecurity\Contract\HttpFoundation\RequestMatcherInterface;

/**
 * Mimics @see \Symfony\Component\Security\Http\FirewallMap.
 */
final class FirewallMap implements FirewallMapInterface
{
    /**
     * @var array|RequestMatcherInterface[][]
     */
    private $map = [];

    public function add(
        RequestMatcherInterface $requestMatcher = null,
        array $listeners = [],
        ExceptionListener $exceptionListener = null
    ) {
        $this->map[] = [$requestMatcher, $listeners, $exceptionListener];
    }

    public function getListeners(IRequest $request) : array
    {
        foreach ($this->map as $elements) {
            if ($elements[0] === null || $elements[0]->matches($request)) {
                return [$elements[1], $elements[2]];
            }
        }

        return [[], null];
    }
}
