<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoChainMethodCallRule\Source;

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
