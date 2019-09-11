<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use Nette\Utils\Strings;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Argument\BoundArgument;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Bind parameters by default:
 * - from "%value_name%"
 * - to "$valueName"
 */
final class AutoBindParametersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $boundArguments = $this->createBoundArgumentsFromParameterBag($containerBuilder->getParameterBag());

        foreach ($containerBuilder->getDefinitions() as $definition) {
            if ($this->shouldSkipDefinition($definition)) {
                continue;
            }

            $reflectionClass = $containerBuilder->getReflectionClass($definition->getClass());
            if ($reflectionClass === null) {
                continue;
            }

            $constructorReflection = $reflectionClass->getConstructor();
            if ($constructorReflection === null) {
                continue;
            }

            // exclude non-scalar parameters
            $parameterNamesToExclude = $this->resolveMethodReflectionNonScalarArgumentNames($constructorReflection);
            $parameterNamesToExclude = array_flip($parameterNamesToExclude);
            $bindings = array_diff_key($boundArguments, $parameterNamesToExclude);

            // config binding has priority over default one
            $bindings = array_merge($definition->getBindings(), $bindings);

            $definition->setBindings($bindings);
        }
    }

    /**
     * @return BoundArgument[]
     */
    private function createBoundArgumentsFromParameterBag(ParameterBagInterface $parameterBag): array
    {
        $boundArguments = [];
        foreach ($parameterBag->all() as $name => $value) {
            // not ready to autowire
            if (! is_string($name)) {
                continue;
            }

            if (Strings::contains($name, '.') || Strings::contains($name, 'env(')) {
                continue;
            }

            $boundArgument = new BoundArgument($value);

            // set used so it doesn't end on exceptions
            [
             $value, $identifier,
            ] = $boundArgument->getValues();
            $boundArgument->setValues([$value, $identifier, true]);

            $parameterGuess = '$' . $this->undescoredToCamelCase($name);
            $boundArguments[$parameterGuess] = $boundArgument;
        }

        return $boundArguments;
    }

    private function shouldSkipDefinition(Definition $definition): bool
    {
        if ($definition->isAbstract()) {
            return true;
        }

        if ($definition instanceof ChildDefinition && $definition->getClass() === null) {
            return true;
        }

        if ($definition->getClass() === null && $definition->getFactory() === null) {
            return true;
        }

        return false;
    }

    /**
     * @return string[]
     */
    private function resolveMethodReflectionNonScalarArgumentNames(ReflectionMethod $reflectionMethod): array
    {
        $argumentNames = [];
        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $typeName = (string) $reflectionParameter->getType();

            // probably not scalar type
            if (isset($typeName[0]) && (Strings::contains($typeName, '\\') || ctype_upper($typeName[0]))) {
                // '$' to be consistent with bind parameter naming
                $argumentNames[] = '$' . $reflectionParameter->name;
            }
        }

        return $argumentNames;
    }

    /**
     * @see https://stackoverflow.com/a/2792045/1348344
     */
    private function undescoredToCamelCase(string $string): string
    {
        $string = str_replace('_', '', ucwords($string, '_'));

        return lcfirst($string);
    }
}
