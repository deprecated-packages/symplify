<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\ObjectCalisthenics\Rules\NoChainMethodCallRule\Source;

final class SomeAllowedFluent
{
    public function yes(): self
    {
        return $this;
    }

    public function please(): self
    {
        return $this;
    }
}
