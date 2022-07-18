<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule\Source\ReturnMethodStatic;

final class SkipStaticReturn
{
    public function run(ReturnMethodStatic $returnMethodStatic)
    {
        $returnMethodStatic->getStatic();
    }
}
