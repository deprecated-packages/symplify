<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoInjectOnFinalRule\Fixture;

class SkipInjectOnNonAbstract
{
    /**
     * @inject
     * @var SomeType
     */
    public $someProperty;
}
