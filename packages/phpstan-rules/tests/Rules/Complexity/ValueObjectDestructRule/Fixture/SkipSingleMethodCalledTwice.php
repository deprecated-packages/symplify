<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ValueObjectDestructRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Complexity\ValueObjectDestructRule\Source\PossiblyService;

final class SkipSingleMethodCalledTwice
{
    public function run(PossiblyService $possiblyService)
    {
        $this->process($possiblyService->run(), $possiblyService->run());
    }

    private function process(int $number, int $anotherNumber)
    {
    }
}
