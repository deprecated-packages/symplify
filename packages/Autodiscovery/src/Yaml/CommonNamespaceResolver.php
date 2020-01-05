<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Yaml;

use Nette\Utils\Strings;

final class CommonNamespaceResolver
{
    /**
     * @param string[] $classes
     * @return string[]
     */
    public function resolve(array $classes, int $nestingLevel): array
    {
        if ($classes === []) {
            return [];
        }

        $namespaces = [];
        foreach ($classes as $class) {
            $namespace = Strings::before($class, '\\', $nestingLevel);
            if ($namespace) {
                $namespaces[] = $namespace;
            }
        }

        if (count($namespaces) > 0) {
            return array_unique($namespaces);
        }

        // reiterate with less strict nesting
        return $this->resolve($classes, --$nestingLevel);
    }
}
