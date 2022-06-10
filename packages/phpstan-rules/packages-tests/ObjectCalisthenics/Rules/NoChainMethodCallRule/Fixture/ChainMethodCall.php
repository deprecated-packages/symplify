<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\ObjectCalisthenics\Rules\NoChainMethodCallRule\Fixture;

final class ChainMethodCall
{
    public function run()
    {
        return $this->also()->more();
    }
}
