<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\Fixture;

final class SkipNonDynamicProperty
{
    public function run()
    {
        Connection::literal;
    }
}
