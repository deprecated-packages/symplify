<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\Fixture;

final class DynamicMethodCall
{
    public function run()
    {
        $this->connection::literal();
    }
}
