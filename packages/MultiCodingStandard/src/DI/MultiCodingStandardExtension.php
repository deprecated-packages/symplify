<?php declare(strict_types=1);

namespace Symplify\MultiCodingStandard\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symplify\MultiCodingStandard\Application\ApplicationRunner;
use Symplify\MultiCodingStandard\Contract\Application\ApplicationInterface;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class MultiCodingStandardExtension extends CompilerExtension
{
    public function loadConfiguration() : void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__.'/../config/services.neon')['services']
        );
    }

    public function beforeCompile() : void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            Application::class,
            Command::class,
            'add'
        );

        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            ApplicationRunner::class,
            ApplicationInterface::class,
            'addApplication'
        );
    }
}
