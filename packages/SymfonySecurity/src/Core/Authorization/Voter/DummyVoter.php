<?php

declare(strict_types=1);

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
