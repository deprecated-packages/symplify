<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckSprintfMatchingTypesRule\Fixture;

final class SkipErrorType
{
    private function resolvePropertyReflection(object $object, string $propertyName)
    {
        $errorMessage = sprintf('Property "$%s" was not found in "%s" class', $propertyName, $object::class);
    }
}
