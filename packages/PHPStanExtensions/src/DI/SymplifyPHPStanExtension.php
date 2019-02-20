<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use PHPStan\Rules\Rule;

final class SymplifyPHPStanExtension extends CompilerExtension
{
    public function beforeCompile(): void
    {
        $containerBuilder = $this->getContainerBuilder();

        $excludedRules = (array) $containerBuilder->parameters['excluded_rules'] ?? [];
        $this->removeExcludedRules($containerBuilder, $excludedRules);
    }

    /**
     * @param string[] $excludedRules
     */
    private function removeExcludedRules(ContainerBuilder $containerBuilder, array $excludedRules): void
    {
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
