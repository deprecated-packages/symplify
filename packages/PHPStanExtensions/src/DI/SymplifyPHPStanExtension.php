<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\DI;

use Nette\DI\CompilerExtension;
use PHPStan\Rules\Rule;

final class SymplifyPHPStanExtension extends CompilerExtension
{
    public function beforeCompile(): void
    {
        $containerBuilder = $this->getContainerBuilder();

        $excludedRules = $containerBuilder->parameters['excluded_rules'] ?? [];
        if ($excludedRules === []) {
            return;
        }

        $ruleDefinitions = $containerBuilder->findByType(Rule::class);

        foreach ($ruleDefinitions as $name => $ruleDefinition) {
            if (! in_array($ruleDefinition->getType(), $excludedRules, true)) {
                continue;
            }

            $containerBuilder->removeDefinition($name);
        }
    }
}
