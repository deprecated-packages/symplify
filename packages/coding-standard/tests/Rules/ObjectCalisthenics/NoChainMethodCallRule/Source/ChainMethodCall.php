<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ObjectCalisthenics\NoChainMethodCallRule\Source;

final class ChainMethodCall
{
    public function run()
    {
        return $this->also()->more();
    }
}
