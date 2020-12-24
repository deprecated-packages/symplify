<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\IfNewTypeThenImplementInterfaceRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\IfNewTypeThenImplementInterfaceRule\Source\SomeParentWithInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;

final class SkipNewWithParentInterface extends SomeParentWithInterface
{
    public function run()
    {
        return new ConfiguredCodeSample('...', '...', []);
    }
}
