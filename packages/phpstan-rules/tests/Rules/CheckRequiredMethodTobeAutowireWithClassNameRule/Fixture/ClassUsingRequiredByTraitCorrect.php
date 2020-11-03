<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckRequiredMethodTobeAutowireWithClassNameRule\Fixture;

final class ClassUsingRequiredByTraitCorrect
{
    use RequiredByTraitCorrect;
}
