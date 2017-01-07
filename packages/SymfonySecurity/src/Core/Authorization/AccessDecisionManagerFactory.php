<?php

declare(strict_types=1);

namespace Symplify\SymfonySecurity\Core\Authorization;

use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Factory for @see AccessDecisionManager.
 */
final class AccessDecisionManagerFactory
{
    /**
     * @var VoterInterface[]
     */
    private $voters = [];

    public function addVoter(VoterInterface $voter)
    {
        $this->voters[] = $voter;
    }

    public function create() : AccessDecisionManagerInterface
    {
        return new AccessDecisionManager($this->voters, AccessDecisionManager::STRATEGY_UNANIMOUS, true);
    }
}
