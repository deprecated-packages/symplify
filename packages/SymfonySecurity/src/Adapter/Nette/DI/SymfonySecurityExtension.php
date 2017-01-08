<?php declare(strict_types=1);

namespace Symplify\SymfonySecurity\Adapter\Nette\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symplify\SymfonySecurity\Contract\Http\FirewallHandlerInterface;
use Symplify\SymfonySecurity\Contract\Http\FirewallMapFactoryInterface;
use Symplify\SymfonySecurity\Contract\HttpFoundation\RequestMatcherInterface;
use Symplify\SymfonySecurity\Core\Authorization\AccessDecisionManagerFactory;

final class SymfonySecurityExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')['services']
        );
    }

    public function beforeCompile()
    {
        $containerBuilder = $this->getContainerBuilder();

        $this->loadAccessDecisionManagerFactoryWithVoters();

        if ($containerBuilder->findByType(FirewallHandlerInterface::class)) {
            $this->loadFirewallMap();
        }
    }

    private function loadAccessDecisionManagerFactoryWithVoters()
    {
        $this->loadMediator(AccessDecisionManagerFactory::class, VoterInterface::class, 'addVoter');
    }

    private function loadFirewallMap()
    {
        $this->loadMediator(FirewallMapFactoryInterface::class, FirewallHandlerInterface::class, 'addFirewallHandler');
        $this->loadMediator(FirewallMapFactoryInterface::class, RequestMatcherInterface::class, 'addRequestMatcher');
    }

    private function loadMediator(string $mediatorClass, string $colleagueClass, string $adderMethod)
    {
        $containerBuilder = $this->getContainerBuilder();

        $mediatorDefinition = $containerBuilder->getDefinition($containerBuilder->getByType($mediatorClass));
        foreach ($containerBuilder->findByType($colleagueClass) as $colleagueDefinition) {
            $mediatorDefinition->addSetup($adderMethod, ['@' . $colleagueDefinition->getClass()]);
        }
    }
}
