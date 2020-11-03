<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredMethodTobeAutowireWithClassNameRule\Fixture;

final class ClassUsingRequiredByTrait
{
    use RequiredByTrait;
}
