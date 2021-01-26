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
        $expectedSuffix = (string) Strings::after($parentClass, '\\', -1);
        $expectedSuffix = $this->resolveExpectedSuffix($expectedSuffix);

        // special case for tests
        if ($expectedSuffix === 'TestCase') {
            return 'Test';
        }

        return $expectedSuffix;
    }

    /**
     * - SomeInterface => Some
     * - SomeAbstract => Some
     * - AbstractSome => Some
     */
    private function resolveExpectedSuffix(string $parentType): string
    {
        if (Strings::endsWith($parentType, 'Interface')) {
            $parentType = Strings::substring($parentType, 0, -strlen('Interface'));
        }

        if (Strings::endsWith($parentType, 'Abstract')) {
            $parentType = Strings::substring($parentType, 0, -strlen('Abstract'));
        }

        if (Strings::startsWith($parentType, 'Abstract')) {
            $parentType = Strings::substring($parentType, strlen('Abstract'));
        }

        return $parentType;
    }
}
