<?php declare(strict_types=1);

namespace Symplify\SymfonySecurityVoters\Adapter\Nette\Authorization;

use Nette\Security\User;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

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

    public function decide(User $user, $reflecitocomponents)
    {

    }

//    public function create() : AccessDecisionManagerInterface
//    {
//        return new AccessDecisionManager($this->voters, AccessDecisionManager::STRATEGY_UNANIMOUS, true);
//    }
}
