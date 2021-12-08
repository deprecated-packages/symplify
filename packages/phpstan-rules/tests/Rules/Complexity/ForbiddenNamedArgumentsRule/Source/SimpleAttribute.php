<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenNamedArgumentsRule\Source;

#[\Attribute]
final class SimpleAttribute
{
    public function __construct(
        private $value
    ) {
    }

}
