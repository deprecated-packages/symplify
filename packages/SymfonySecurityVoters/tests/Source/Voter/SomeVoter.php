<?php declare(strict_types=1);

namespace Symplify\SymfonySecurityVoters\Tests\Source\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class SomeVoter implements VoterInterface
{
    /**
     * @param TokenInterface $token
     * @param mixed $object
     * @param mixed[] $attributes
     */
    public function vote(TokenInterface $token, $object, array $attributes): int
    {
        return -1;
    }
}
