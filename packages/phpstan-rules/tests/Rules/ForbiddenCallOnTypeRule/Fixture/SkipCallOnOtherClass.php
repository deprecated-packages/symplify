<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenCallOnTypeRule\Fixture;

use DateTime;

final class SkipCallOnOtherClass
{
    /**
     * @var DateTime
     */
    private $d;

    public function __contruct(DateTime $d)
    {
        $this->d = $d;
    }

    public function call()
    {
        $this->d->format('Y-m-d');
    }
}
