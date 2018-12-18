<?php declare(strict_types=1);

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
        $namespaces = [];
        foreach ($classes as $class) {
            $namespace = Strings::before($class, '\\', $nestingLevel);
            if ($namespace) {
                $namespaces[] = $namespace;
            }
        }

        return array_unique($namespaces);
    }
}
