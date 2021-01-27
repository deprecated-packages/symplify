<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\Fixture;

final class SkipNonDynamicPropertyCall
{
    public function run()
    {
        Connection::literal;
    }
}
