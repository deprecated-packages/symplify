<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireClassTypeInClassMethodByTypeRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\RequireClassTypeInClassMethodByTypeRule\Source\AnyParentGetTypesInterface;

final class SkipEmptyArray implements AnyParentGetTypesInterface
{
    public function getNodeTypes(): array
    {
        return [];
    }
}
