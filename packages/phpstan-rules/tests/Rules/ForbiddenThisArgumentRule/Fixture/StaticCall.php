<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\Source\SomeStaticService;

final class StaticCall
{
    public function run()
    {
        SomeStaticService::someMethod($this);
    }
}
