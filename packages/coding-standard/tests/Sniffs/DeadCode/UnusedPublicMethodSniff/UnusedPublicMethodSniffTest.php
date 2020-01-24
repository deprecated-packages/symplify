<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\DeadCode\UnusedPublicMethodSniff;

use Symplify\CodingStandard\Sniffs\DeadCode\UnusedPublicMethodSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class UnusedPublicMethodSniffTest extends AbstractCheckerTestCase
{
    public function testCorrect(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/Fixture/skip_entity_calls.php.inc');
        $this->doTestCorrectFile(__DIR__ . '/Fixture/correct.php.inc');
    }

    public function testWrong(): void
    {
        $this->markTestSkipped('False positives');

        $this->doTestWrongFile(__DIR__ . '/Fixture/wrong.php.inc');
        $this->doTestWrongFile(__DIR__ . '/Fixture/wrong2.php.inc');
    }

    protected function getCheckerClass(): string
    {
        return UnusedPublicMethodSniff::class;
    }
}
