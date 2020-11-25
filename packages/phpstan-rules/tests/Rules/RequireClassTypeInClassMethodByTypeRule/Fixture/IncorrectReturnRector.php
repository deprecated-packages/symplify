<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireClassTypeInClassMethodByTypeRule\Fixture;

use PhpParser\Node\Stmt\Class_;
use Symplify\PHPStanRules\Tests\Rules\RequireClassTypeInClassMethodByTypeRule\Source\AnyParentGetTypesInterface;
use Symplify\PHPStanRules\Tests\Rules\RequireClassTypeInClassMethodByTypeRule\Source\SomeType;

final class IncorrectReturnRector implements AnyParentGetTypesInterface
{
    public function getNodeTypes(): array
    {
        return [SomeType::class, Class_::class];
    }
}
