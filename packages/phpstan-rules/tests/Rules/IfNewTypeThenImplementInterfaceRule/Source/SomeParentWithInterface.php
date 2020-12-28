<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\IfNewTypeThenImplementInterfaceRule\Source;

use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;

abstract class SomeParentWithInterface implements ConfigurableRuleInterface
{
}
