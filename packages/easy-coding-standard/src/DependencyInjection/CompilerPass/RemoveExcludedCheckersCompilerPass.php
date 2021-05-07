<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\EasyCodingStandard\ValueObject\Option;

final class RemoveExcludedCheckersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $excludedCheckers = $this->getExcludedCheckersFromParameterBag($containerBuilder->getParameterBag());

        $definitions = $containerBuilder->getDefinitions();
        foreach ($definitions as $id => $definition) {
            if (! in_array($definition->getClass(), $excludedCheckers, true)) {
                continue;
            }

            $containerBuilder->removeDefinition($id);
        }
    }

    /**
     * @return array<int, class-string>
     */
    private function getExcludedCheckersFromParameterBag(ParameterBagInterface $parameterBag): array
    {

        // parts of "skip" parameter
        if (! $parameterBag->has(Option::SKIP)) {
            return [];
        }

        $excludedCheckers = [];

        $skip = (array) $parameterBag->get(Option::SKIP);
        foreach ($skip as $key => $value) {
            // "SomeClass::class" => null
            if (is_string($key) && class_exists($key) && $value === null) {
                $excludedCheckers[] = $key;
            }

            // "SomeClass::class"
            if (is_int($key) && class_exists($value)) {
                $excludedCheckers[] = $value;
            }
        }

        return array_unique($excludedCheckers);
    }
}
