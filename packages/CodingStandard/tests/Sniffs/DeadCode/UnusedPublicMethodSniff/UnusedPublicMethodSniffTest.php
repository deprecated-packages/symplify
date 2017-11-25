<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\DeadCode\UnusedPublicMethodSniff;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Sniffs\DeadCode\UnusedPublicMethodSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class UnusedPublicMethodSniffTest extends AbstractSniffTestCase
{
    public function testWrong(): void
    {
        $this->doTestWrongFile(__DIR__ . '/wrong/wrong.php.inc');
    }

    protected function createSniff(): Sniff
    {
        return new UnusedPublicMethodSniff();
    }
}
