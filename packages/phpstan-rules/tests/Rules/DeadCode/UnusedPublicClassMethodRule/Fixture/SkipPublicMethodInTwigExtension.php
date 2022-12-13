<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassMethodRule\Fixture;

use Twig\Extension\AbstractExtension;

final class SkipPublicMethodInTwigExtension extends AbstractExtension
{
    public function someFilterMethod()
    {
    }
}
