<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoTraitExceptItsMethodsPublicAndRequired\Fixture;

final class ClassContantFetch
{
    public const FOO = 'Foo';
}

ClassContantFetch::FOO;

