<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTraitMethodOnlyDelegateOtherClassRule\Fixture;

use DateTime;

trait Delegate
{
    private $d;

    public function __construct(DateTime $d)
    {
        $this->d = $d;
    }

    public function run()
    {
        return $this->d->format('Y-m-d H:i:s');
    }
}
