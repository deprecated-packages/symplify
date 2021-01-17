<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenFuncCallRule\Fixture;

final class PropertyExists
{
    public function run($element)
    {
        return property_exists($element, 'items');
    }
}
