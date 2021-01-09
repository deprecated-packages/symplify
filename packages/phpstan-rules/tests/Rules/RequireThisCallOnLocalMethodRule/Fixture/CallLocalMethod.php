<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireThisCallOnLocalMethodRule\Fixture;

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
