<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireChildClassGenericTypeRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\RequireChildClassGenericTypeRule\Source\AbstractClassWithTemplate;

/**
 * @extends AbstractClassWithTemplate<SomeType>
 */
final class SkipCorrect extends AbstractClassWithTemplate
{
}
