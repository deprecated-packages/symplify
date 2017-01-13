<?php declare(strict_types=1);

namespace Symplify\SymfonySecurityVoters\Authorization;

use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Factory for @see AccessDecisionManager.
 */
final class AccessDecisionManager
{
    /**
     * @var VoterInterface[]
     */
    private $voters = [];

    public function addVoter(VoterInterface $voter) : void
    {
        $this->voters[] = $voter;
    }

    public function create() : AccessDecisionManagerInterface
    {
        return new AccessDecisionManager($this->voters, AccessDecisionManager::STRATEGY_UNANIMOUS, true);
    }
}
