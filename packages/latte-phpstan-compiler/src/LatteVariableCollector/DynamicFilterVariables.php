<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\LatteVariableCollector;

use Nette\Utils\Strings;
use PHPStan\Type\ObjectType;
use ReflectionClass;
use ReflectionException;
use Symplify\LattePHPStanCompiler\Contract\LatteVariableCollectorInterface;
use Symplify\TemplatePHPStanCompiler\ValueObject\VariableAndType;

final class DynamicFilterVariables implements LatteVariableCollectorInterface
{
    /**
     * @param array<string, string|array{string, string}> $latteFilters
     */
    public function __construct(
        private array $latteFilters
    ) {
    }

    /**
     * @return VariableAndType[]
     */
    public function getVariablesAndTypes(): array
    {
        $variablesAndTypes = [];
        foreach ($this->latteFilters as $latteFilter) {
            if (is_string($latteFilter)) {
                continue;
            }

            $className = $latteFilter[0];
            $methodName = $latteFilter[1];

            try {
                $reflectionClass = new ReflectionClass($className);
                $reflectionMethod = $reflectionClass->getMethod($methodName);

                if ($reflectionMethod->isStatic()) {
                    continue;
                }

                $variableName = Strings::firstLower(Strings::replace($className, '/\\\\/', '')) . 'Filter';
                $variablesAndTypes[] = new VariableAndType($variableName, new ObjectType($className));
            } catch (ReflectionException $e) {
                continue;
            }
        }
        return $variablesAndTypes;
    }
}
