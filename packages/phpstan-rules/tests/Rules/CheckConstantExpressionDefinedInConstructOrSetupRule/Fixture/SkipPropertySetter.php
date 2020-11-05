<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class SkipPropertySetter
{
    /**
     * @var bool
     */
    private $isEnabled;

    public function enable()
    {
        $this->isEnabled = true;
    }
}
