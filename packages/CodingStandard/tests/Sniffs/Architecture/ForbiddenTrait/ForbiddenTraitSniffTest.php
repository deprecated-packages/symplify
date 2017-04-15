<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Architecture\ForbiddenTrait;

use Symplify\CodingStandard\Sniffs\Architecture\ForbiddenTraitSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class ForbiddenTraitSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(ForbiddenTraitSniff::class, __DIR__);
    }
}
