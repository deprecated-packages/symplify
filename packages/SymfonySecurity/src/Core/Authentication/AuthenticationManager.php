<?php

declare(strict_types=1);

/*
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace Symplify\SymfonySecurity\Core\Authentication;

use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Dummy implementation with no custom logic,
 * just to pass Token back.
 */
final class AuthenticationManager implements AuthenticationManagerInterface
{
    public function authenticate(TokenInterface $token)
    {
        return $token;
    }
}
