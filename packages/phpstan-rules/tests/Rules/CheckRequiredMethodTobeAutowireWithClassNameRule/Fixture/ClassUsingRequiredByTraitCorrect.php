<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredMethodTobeAutowireWithClassNameRule\Fixture;

final class ClassUsingRequiredByTraitCorrect
{
    use RequiredByTraitCorrect;
}
