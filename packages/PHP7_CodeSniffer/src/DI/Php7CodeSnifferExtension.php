<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Contract\Configuration\OptionResolver\OptionResolverInterface;

final class Php7CodeSnifferExtension extends CompilerExtension
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
        $this->loadOptionResolversToConfigurationResolver();
    }

    private function loadOptionResolversToConfigurationResolver() : void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            ConfigurationResolver::class,
            OptionResolverInterface::class,
            'addOptionResolver'
        );
    }
}
