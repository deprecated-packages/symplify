<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Naming;

use Nette\Utils\Strings;

/**
 * @see \Symplify\PHPStanRules\Tests\Naming\ClassToSuffixResolverTest
 */
final class ClassToSuffixResolver
{
    public function resolveFromClass(string $parentClass): string
    {
        $expectedSuffix = \str_contains($parentClass, '\\') ? (string) Strings::after(
            $parentClass,
            '\\',
            -1
        ) : $parentClass;

        $expectedSuffix = $this->removeAbstractInterfacePrefixSuffix($expectedSuffix);

        // special case for tests
        if ($expectedSuffix === 'TestCase') {
            return 'Test';
        }

        return $expectedSuffix;
    }

    private function removeAbstractInterfacePrefixSuffix(string $parentType): string
    {
        if (\str_ends_with($parentType, 'Interface')) {
            $parentType = Strings::substring($parentType, 0, -strlen('Interface'));
        }

        if (\str_ends_with($parentType, 'Abstract')) {
            $parentType = Strings::substring($parentType, 0, -strlen('Abstract'));
        }

        if (\str_starts_with($parentType, 'Abstract')) {
            return Strings::substring($parentType, strlen('Abstract'));
        }

        return $parentType;
    }
}
