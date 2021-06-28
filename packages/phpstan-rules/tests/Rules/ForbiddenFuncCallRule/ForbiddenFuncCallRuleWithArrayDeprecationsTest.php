<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenFuncCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule;

/**
 * @extends ForbiddenFuncCallRuleWithDeprecationsTest<ForbiddenFuncCallRule>
 */
final class ForbiddenFuncCallRuleWithArrayDeprecationsTest extends ForbiddenFuncCallRuleWithDeprecationsTest
{
    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ForbiddenFuncCallRule::class, __DIR__ . '/config/configured_rule_with_array_deprecations.neon');
    }
}
