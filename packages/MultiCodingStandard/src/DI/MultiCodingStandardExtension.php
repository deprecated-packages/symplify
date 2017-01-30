<?php declare(strict_types=1);

namespace Symplify\MultiCodingStandard\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

final class MultiCodingStandardExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $this->loadServicesFromConfigPath(__DIR__.'/../config/services.neon');
    }

    private function loadServicesFromConfigPath(string $configPath)
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile($configPath)['services']
        );
    }
}
