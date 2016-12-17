<?php

declare(strict_types=1);

/*
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace Symplify\SymfonySecurity\Core\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class DummyVoter implements VoterInterface
{
    /**
     * @param TokenInterface $token
     * @param mixed $object
     * @param array $attributes
     */
    public function vote(TokenInterface $token, $object, array $attributes) : int
    {
        return self::ACCESS_ABSTAIN;
    }
}
