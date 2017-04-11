<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Adapter\Nette\DI;

use Nette\Application\UI\ITemplateFactory;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

final class IndependentSingleActionPresenterExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        $containerBuilder = $this->getContainerBuilder();

        Compiler::loadDefinitions(
            $containerBuilder,
            $this->loadFromFile(__DIR__ . '/../config/services/common.neon')
        );

        if ($containerBuilder->findByType(ITemplateFactory::class)) {
            Compiler::loadDefinitions(
                $containerBuilder,
                $this->loadFromFile(__DIR__ . '/../config/services/template-renderer.neon')
            );
        }
    }
}
