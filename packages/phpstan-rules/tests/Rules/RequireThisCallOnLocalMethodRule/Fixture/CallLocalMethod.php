<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireThisOnParentMethodCallRule\Fixture;

class CallLocalMethod
{
    public function run()
    {
        self::execute();
    }

    private function execute()
    {
    }
}
