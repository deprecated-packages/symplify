<?php declare(strict_types=1);

namespace Symplify\SymfonySecurityVoters\Adapter\Nette\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;
use Symplify\SymfonySecurityVoters\Authorization\AccessDecisionManagerFactory;

final class SymfonySecurityExtension extends CompilerExtension
{
    public function loadConfiguration() : void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')['services']
        );
    }

    public function beforeCompile() : void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            AccessDecisionManagerFactory::class,
            VoterInterface::class,
            'addVoter'
        );
    }
}
