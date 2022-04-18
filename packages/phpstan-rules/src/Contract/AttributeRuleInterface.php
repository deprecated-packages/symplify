<?php

namespace Symplify\PHPStanRules\Contract;

use PhpParser\Node\Attribute;
use PHPStan\Analyser\Scope;

interface AttributeRuleInterface
{
    /**
     * @return string[]
     */
    public function processAttribute(Attribute $attribute, Scope $scope): array;
}
