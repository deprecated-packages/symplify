<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\IfNewTypeThenImplementInterfaceRule\Fixture;

use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;

final class WithNew
{
    public function run()
    {
        return new ConfiguredCodeSample('...', '...', []);
    }
}
