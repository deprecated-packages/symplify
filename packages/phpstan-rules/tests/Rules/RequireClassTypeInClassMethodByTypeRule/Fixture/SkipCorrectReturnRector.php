<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireClassTypeInClassMethodByTypeRule\Fixture;

use PhpParser\Node\Scalar\String_;
use Symplify\PHPStanRules\Tests\Rules\RequireClassTypeInClassMethodByTypeRule\Source\AnyParentGetTypesInterface;

final class SkipCorrectReturnRector implements AnyParentGetTypesInterface
{
    public function getNodeTypes(): array
    {
        return [String_::class];
    }
}
