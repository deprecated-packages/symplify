<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\IfNewTypeThenImplementInterfaceRule\Fixture;

use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;

final class SkipNewWithInterface implements ConfigurableRuleInterface
{
    public function run()
    {
        return new ConfiguredCodeSample('...', '...', []);
    }
}
