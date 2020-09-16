<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticPropertyRule\Fixture;

final class SomeStaticPropertyWithoutModifier
{
    private $nonStaticFileNames = [];
    static $customFileNames = [];
}
