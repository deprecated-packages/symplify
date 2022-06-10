<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\NoInjectOnFinalRule\Fixture;

use Symplify\PHPStanRules\Tests\Nette\Rules\NoInjectOnFinalRule\Source\SomeType;

class SkipInjectOnNonAbstract
{
    /**
     * @inject
     * @var SomeType
     */
    public $someProperty;
}
