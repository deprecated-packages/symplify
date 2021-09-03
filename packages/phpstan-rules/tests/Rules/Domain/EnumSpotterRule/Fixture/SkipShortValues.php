<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\EnumSpotterRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Domain\EnumSpotterRule\Source\AnotherFlashAdder;

final class SkipShortValues
{
    public function apply(AnotherFlashAdder $flashAdder)
    {
        $flashAdder->addFlash('some message', 'id');
    }

    public function applyAgain(AnotherFlashAdder $flashAdder)
    {
        $flashAdder->addFlash('another message', 'id');
    }
}
