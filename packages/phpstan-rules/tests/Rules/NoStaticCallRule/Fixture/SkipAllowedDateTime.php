<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Fixture;

use Nette\Utils\DateTime;

class SkipAllowedDateTime
{
    public function run()
    {
        return DateTime::from('now');
    }
}
