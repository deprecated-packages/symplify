<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\Fixture;

final class DynamicPropertyCall
{
    public function run()
    {
        $this->connection::literal;
    }
}
