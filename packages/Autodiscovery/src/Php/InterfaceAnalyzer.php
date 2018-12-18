<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Php;

use function Safe\class_implements;

final class InterfaceAnalyzer
{
    public function isInterfaceOnlyImplementation(string $interface, string $class): bool
    {
        if (! interface_exists($interface)) {
            return false;
        }

        if (! class_exists($class)) {
            return false;
        }

        $interfaceImplementers = $this->getInterfaceImplementers($interface);
        if (! in_array($class, $interfaceImplementers, true)) {
            return false;
        }

        if (count($interfaceImplementers) !== 1) {
            return false;
        }

        return true;
    }

    /**
     * @return string[]
     */
    private function getInterfaceImplementers(string $interface): array
    {
        $interfaceImplementers = [];
        foreach (get_declared_classes() as $className) {
            if (in_array($interface, class_implements($className), true)) {
                $interfaceImplementers[] = $className;
            }
        }

        return $interfaceImplementers;
    }
}
