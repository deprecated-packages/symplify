<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoTraitExceptForSymfonyClassRule\Fixture;

final class SomeClassWithTrait
{
    use SomeTrait;
}