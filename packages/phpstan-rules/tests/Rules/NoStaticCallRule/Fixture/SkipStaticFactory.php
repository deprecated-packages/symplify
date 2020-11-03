<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoStaticCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoStaticCallRule\Source\ClassWithFactory;

final class SkipStaticFactory
{
    public function run()
    {
        return ClassWithFactory::create();
    }
}
