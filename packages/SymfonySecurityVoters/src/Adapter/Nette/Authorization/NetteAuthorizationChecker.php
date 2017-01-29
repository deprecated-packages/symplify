<?php declare(strict_types=1);

namespace Symplify\SymfonySecurityVoters\Adapter\Nette\Authorization;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class NetteAuthorizationChecker implements AuthorizationCheckerInterface
{
    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * @var AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    public function __construct(TokenInterface $token, AccessDecisionManagerInterface $accessDecisionManager)
    {
        $this->token = $token;
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @param mixed $attributes
     * @param mixed $object
     */
    public function isGranted($attributes, $object = null) : bool
    {
        $attributes = (array) $attributes;

        return $this->accessDecisionManager->decide($this->token, $attributes, $object);
    }
}
