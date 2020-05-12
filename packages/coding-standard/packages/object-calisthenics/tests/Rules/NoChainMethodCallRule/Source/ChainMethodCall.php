<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\NoChainMethodCallRule\Source;

final class ChainMethodCall
{
    public function run()
    {
        return $this->also()->more();
    }
}
