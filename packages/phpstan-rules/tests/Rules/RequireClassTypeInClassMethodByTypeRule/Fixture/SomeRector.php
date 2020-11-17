<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireClassTypeInClassMethodByTypeRule\Fixture;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Symplify\PHPStanRules\Tests\Rules\RequireClassTypeInClassMethodByTypeRule\Source\AnyParentGetTypesInterface;

final class SomeRector implements AnyParentGetTypesInterface
{
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Class_::class];
    }
}
