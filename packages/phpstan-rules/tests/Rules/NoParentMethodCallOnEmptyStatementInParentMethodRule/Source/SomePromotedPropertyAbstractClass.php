<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule\Source;

abstract class SomePromotedPropertyAbstractClass
{
    public function __construct(
        private string $value
    ) {
    }
}
