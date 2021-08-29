<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\EnumSpotterRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Domain\EnumSpotterRule\Source\FlashAdder;

final class SkipNotRepeatedUse
{
    public function run(FlashAdder $flashAdder)
    {
        $flashAdder->addFlash('another message', 'info');
    }
}
