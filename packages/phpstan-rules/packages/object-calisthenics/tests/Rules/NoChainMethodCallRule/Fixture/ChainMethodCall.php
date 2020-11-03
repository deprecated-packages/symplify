<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoChainMethodCallRule\Fixture;

final class ChainMethodCall
{
    public function run()
    {
        return $this->also()->more();
    }
}
