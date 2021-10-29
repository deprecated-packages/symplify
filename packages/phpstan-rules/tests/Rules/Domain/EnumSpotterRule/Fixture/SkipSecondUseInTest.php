<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\EnumSpotterRule\Fixture;

use PHPUnit\Framework\TestCase;
use Symplify\PHPStanRules\Tests\Rules\Domain\EnumSpotterRule\Source\AnotherFlashAdder;

final class SkipSecondUseInTest extends TestCase
{
    public function apply(AnotherFlashAdder $flashAdder)
    {
        $flashAdder->addFlash('some message', 'info');
    }
}
