<?php declare(strict_types=1);

namespace Symplify\SymfonySecurityVoters\Adapter\Nette\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

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
        $containerBuilder = $this->getContainerBuilder();
        $voterDefinitions = $containerBuilder->findByType(VoterInterface::class);

        $containerBuilder->getDefinitionByType(AccessDecisionManager::class)
            ->addSetup('setVoters', array_keys($voterDefinitions));
    }
}
