<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

final class Php7CodeSnifferExtension extends CompilerExtension
{
    public function loadConfiguration() : void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')['services']
        );
    }
}
