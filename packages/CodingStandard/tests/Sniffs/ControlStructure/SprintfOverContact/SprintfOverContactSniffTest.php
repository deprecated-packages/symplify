<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\ControlStructure\SprintfOverContact;

use Symplify\CodingStandard\Sniffs\ControlStructure\SprintfOverContactSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class SprintfOverContactSniffTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong.php.inc', __DIR__ . '/Fixture/correct.php.inc']);
    }

    protected function getCheckerClass(): string
    {
        return SprintfOverContactSniff::class;
    }
}
