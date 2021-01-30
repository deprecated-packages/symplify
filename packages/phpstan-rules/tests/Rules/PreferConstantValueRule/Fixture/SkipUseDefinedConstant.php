<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferConstantValueRule\Fixture;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;

final class SkipUseDefinedConstant
{
    public function run()
    {
        return ComposerJsonSection::REQUIRE;
    }

    public function run2()
    {
        return ComposerJsonSection::REQUIRE_DEV;
    }
}
