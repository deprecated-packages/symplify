<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Fixture;

final class ParentMethodCallOverride extends ParentClass
{
    protected function setUp(): void
    {
        parent::setUp();

        echo 'override';
    }
}
