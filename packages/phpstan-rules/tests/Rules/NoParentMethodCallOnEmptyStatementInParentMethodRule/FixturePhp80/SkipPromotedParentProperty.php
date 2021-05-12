<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule\FixturePhp80;

use Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule\Source\SomePromotedPropertyAbstractClass;

final class SkipPromotedParentProperty extends SomePromotedPropertyAbstractClass
{
    public function __construct(string $value)
    {
        parent::__construct($value);
    }
}
