<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\EnumSpotterRule\Fixture;

use PHPStan\Testing\PHPStanTestCase;
use Symplify\PHPStanRules\Tests\Rules\Domain\EnumSpotterRule\Source\AnotherFlashAdder;

final class SkipFirstUseInTest extends PHPStanTestCase
{
    public function apply(AnotherFlashAdder $flashAdder)
    {
        $flashAdder->addFlash('some message', 'info');
    }
}
