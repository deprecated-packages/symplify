<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Adapter\Laravel\Container;

use Nette\Utils\Strings;
use ReflectionFunction;

final class DefinitionFinder
{
    /**
     * @param mixed[] $definitions
     * @param string $type
     * @return mixed[]
     */
    public static function findAllByType(array $definitions, string $type): array
    {
        $filteredDefinitions = [];
        foreach ($definitions as $name => $definition) {
            if (self::isDefinitionOfType($definition, $type)) {
                $filteredDefinitions[$name] = $definition;
            }
        }

        return $filteredDefinitions;
    }

    /**
     * @param mixed[] $definition
     * @param string $type
     */
    private static function isDefinitionOfType(array $definition, string $type): bool
    {
        // todo: skip later...
        // todo: detect if implements Interface before creating?
        // use some class map like in Symfony?
        // https://laravel.com/docs/5.4/container#container-events
        $closureReflection = new ReflectionFunction($definition['concrete']);

        if (self::isLaravelSystem($closureReflection)) {
            return false;
        }

        $staticVariables = $closureReflection->getStaticVariables();

        if (self::hasVariableOfNameAndType($staticVariables, 'abstract', $type)) {
            return true;
        }

        if (self::hasVariableOfNameAndType($staticVariables, 'variable', $type)) {
            return true;
        }

        // closure explicit return type
        if ((string) $closureReflection->getReturnType() === $type) {
            return true;
        }

        return false;
    }

    private static function isLaravelSystem(ReflectionFunction $closureReflection): bool
    {
        return Strings::startsWith($closureReflection->name, 'Illuminate') &&
            Strings::startsWith($closureReflection->name, 'Illuminate\\Container') === false;
    }

    /**
     * @param string[] $staticVariables
     * @param string $name
     * @param string $classOrInterfaceType
     */
    private static function hasVariableOfNameAndType(array $staticVariables, string $name, string $classOrInterfaceType): bool
    {
        if (! isset($staticVariables[$name])) {
            return false;
        }

        return is_a($staticVariables[$name], $classOrInterfaceType, true);
    }
}

